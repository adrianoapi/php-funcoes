<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();
require_once('../Connections/hhsystem.php');
include('../admin/restrito.php');
require_once('../../hhsystem/funcoes/funcoes.php');
require_once('../admin/geral.php');
include('../admin/config.php');
require_once('../../hhsystem/funcoes/Sajax.php');
require_once('../vendas/funcoesnota.php');


//======================================================================
// ALGORITIMO PARA RECEBER PEDIDOS DO HUB2B
//======================================================================

    
$con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
$funcoes = new FuncoesNota( $con, 0, 2, 1, 'S', 0 );

/*
 * Fluxo do Algoritimo
 * 
 * 1. Ao abrir a pagina, a API solicita os ID's de pedidos ao WS da Hub2b e
 *    os armazena em um array;
 * 
 * 2. Faz um forecah com o array de ID's gerado e cria um novo array atraves do 
 *    retorno da consulta de pedidos no WS;
 * 
 * 3. Pega o array de pedidos, faz um foreach de pdedidos e realiza as
 *    seguintes acoes necessarias;
 *  
 *      3.1.0. Verifica se ja existe o pedido cadastrado na HUB2B_PEDIDO
 *      3.1.1. Se existe, faz o update do status no HUB2B_PEDIDO, NF e HUB2B
 *      3.1.x.            faz o update do codigo de rastreio, caso exista
 *      3.1.2. Se nao existe, insere pedido no HUB2B_PEDIDO e regista NF
 *      3.2.0. Consulta PESSOA para saber se ja existe cadastro
 *      3.2.1. Se existe, retorna apenas PESSOA_CONTROLE
 *      3.2.2. Se nao existe, insere um novo registro e retonra PESSOA_CONTROLE
 *      3.3.0. Consulta PESSOA_CONTATO para saber se ja existe cadastro
 *      3.3.1. Se existe, retorna apenas PESSOA_CONTROLE
 *      3.3.2. Se nao existe, insere um novo registro e retonra PESSOA_CONTROLE
 *      3.4.0. Consulta PESSOA_MEIOCONTATO para saber se ja existe cadastro
 *      3.4.1. Se nao existe, registra PESSOA_MEIOCONTATO (e-mail e/ou telefone)
 *      3.5.0. Registra NF
 *      3.6.0. Registra NFITEM
 *      3.7.0. Registra o Endereco de entrega
 *      3.8.0. Registra o Destinatario
 *      3.9.0. Update na nota
 * 
 */
    
//----------------------------------------------------------------------
// Funcoes de auxilio ao algoritimo
//----------------------------------------------------------------------   

/**
 * Define o status do pedido
 * @param type $status
 * @return int
 */
function define_status($status){
    
    switch ($status) {
        case 'payment-pending':
            return 1;
            break;
        case 'payment-approved':
            return 2;
            break;
        case 'order-accepted':
            return 3;
            break;
        case 'invoiced':
            return 4;
            break;
        case 'shipped':
            return 5;
            break;
        case 'delivered':
            return 6;
            break;
        case 'canceled':
            return 0;
            break;
        default:
            return 1;
            break;
    }
    
}

/**
 * Da um comando <pre> no array ou string com opcao de matar o processo
 * @param type $data
 * @param type $die
 */
function debug($data, $die = FALSE) {
    echo "<pre>";
    if(is_array($data)){
        print_r($data);
    }else{
        echo $data;
    }
    echo "</pre>";
    if($die){
        die();
    }
}

//----------------------------------------------------------------------
// Funcoes para interacao com banco de dados
//----------------------------------------------------------------------  

/**
 * Funcao para consultar pedido na tabela HUB2B_PEDIDO
 */   
function select_pedido_hub2b($HUB2B_PRODUTO_CONTROLE = NULL) {

    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $c_query  = "SELECT * FROM HUB2B_PEDIDO ";
    $c_query .= " WHERE HUB2B_PEDIDO_CONTROLE = '$HUB2B_PRODUTO_CONTROLE' ";
    $rs       = mysql_query($c_query, $con) or die(mysql_error());
    $rst      = mysql_fetch_assoc($rs);
    
    return $rst;
}

function select_nf_cfop($NF) {

    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $c_query  = "SELECT NF.NFCODIGO, NF.RASTREIOPEDIDO, NF.VALORNF, NF.CHAVENFE, NF.SERIENF, CFOP.CODIGO FROM NF ";
    $c_query .= " INNER JOIN CFOP ON CFOP.CFOP_CONTROLE = NF.CFOP_CONTROLE ";
    $c_query .= " WHERE NF.NF_CONTROLE = $NF ";
    $rs       = mysql_query($c_query, $con) or die(mysql_error());
    $rst      = mysql_fetch_assoc($rs);
    
    return $rst;
}

/**
 * Funcao para inserir pedido na tabela HUB2B_PEDIDO
 */   
function insert_pedido_hub2b($HUB2B_PEDIDO_CONTROLE, $STATUS, $MARKETPLACE_CONTROLE) {

    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);

    $c_query = "INSERT INTO HUB2B_PEDIDO SET ";
    $c_query .= " HUB2B_PEDIDO_CONTROLE = '$HUB2B_PEDIDO_CONTROLE',  ";
    $c_query .= " STATUS                = '$STATUS',                 ";
    $c_query .= " MARKETPLACE_CONTROLE  = '$MARKETPLACE_CONTROLE'    ";
    $res = mysql_query($c_query,$con);
    return $res;
}

/**
 * Funcao para inserir pedido na tabela HUB2B_PEDIDO
 */   
function inserir_nf_pedido_hub2b($PEDIDO, $NF_CONTROLE) {

    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);

    $c_query = "UPDATE HUB2B_PEDIDO SET ";
    $c_query .= " NF_CONTROLE   = '$NF_CONTROLE'    ";
    $c_query .= " WHERE HUB2B_PEDIDO_CONTROLE = '$PEDIDO' ";
    $res = mysql_query($c_query,$con);
    return $res;
}

/**
 * Funcao para inserir dados na tabela PESSOA
 */   
function insert_pessoa($NOME, $NOMEUSUAL, $CPF) {

    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);

    $c_query = "INSERT INTO PESSOA SET      ";
    $c_query .= " NOME      = '$NOME',      ";
    $c_query .= " NOMEUSUAL = '$NOMEUSUAL', ";
    $c_query .= " CPF_CNPJ  = '$CPF'        ";
    $res = mysql_query($c_query,$con);
    if($res){
        return mysql_insert_id($con);
    }
}

/**
 * Consulta pessoa pelo CPF
 * @param type $CPF
 * @return PESSOA_CONTROLE
 */
function consulta_pessoa($CPF) {
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $c_query  = "SELECT * FROM PESSOA ";
    $c_query .= " WHERE CPF_CNPJ = '{$CPF}' ";
    $c_query .= " LIMIT 1 ";
    $c_rst    = mysql_query($c_query, $con) or die(mysql_error());
    $c_row    = mysql_fetch_assoc($c_rst);
    return $c_row['PESSOA_CONTROLE'];
}

/**
 * Consulta pessoa contato pelo PESSOA_CONTROLE
 * @param type $PESSOA_CONTROLE
 * @return PESSOA_CONTATO_CONTROLE
 */
function consulta_pessoa_contato($PESSOA_CONTROLE) {
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $c_query  = "SELECT * FROM PESSOA_CONTATO ";
    $c_query .= " WHERE PESSOA_CONTROLE = {$PESSOA_CONTROLE} ";
    $c_query .= " LIMIT 1 ";
    $c_rst    = mysql_query($c_query, $con) or die(mysql_error());
    $c_row    = mysql_fetch_assoc($c_rst);
    return $c_row['PESSOA_CONTATO_CONTROLE'];
}

/**
 * Funcao para inserir dados na tabela PESSOA_CONTATO
 */   
function insert_pessoa_contato($PESSOA_CONTROLE, $TIPOCONTATO_CONTROLE, $NOME, $STATUS) {

    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);

    $c_query = "INSERT INTO PESSOA_CONTATO SET                    ";
    $c_query .= " PESSOA_CONTROLE      = '$PESSOA_CONTROLE',      ";
    $c_query .= " TIPOCONTATO_CONTROLE = '$TIPOCONTATO_CONTROLE', ";
    $c_query .= " NOME                 = '$NOME',                 ";
    $c_query .= " STATUS               = '$STATUS'                ";
    $res = mysql_query($c_query,$con);
    if($res){
        return mysql_insert_id($con);
    }
}

function consulta_pessoa_meio_contato($PESSOA_CONTROLE, $PESSOA_CONTATO_CONTROLE, $TIPOMEIOCONTATO_CONTROLE, $VALOR, $STATUS) {
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $c_query  = "SELECT * FROM PESSOA_MEIOCONTATO ";
    $c_query .= " WHERE PESSOA_CONTROLE        = {$PESSOA_CONTROLE}          ";
    $c_query .= " AND PESSOA_CONTATO_CONTROLE  = {$PESSOA_CONTATO_CONTROLE}  ";
    $c_query .= " AND TIPOMEIOCONTATO_CONTROLE = {$TIPOMEIOCONTATO_CONTROLE} ";
    $c_query .= " AND VALOR                    = '{$VALOR}'                  ";
    $c_query .= " AND STATUS                   = '{$STATUS}'                 ";
    $c_query .= " LIMIT 1 ";
    $c_rst    = mysql_query($c_query, $con) or die(mysql_error());
    $c_row    = mysql_fetch_assoc($c_rst);
    return $c_row['PESSOA_CONTATO_CONTROLE'];
}

/**
 * Funcao para inserir dados na tabela PESSOA_MEIOCONTATO
 */   
function insert_pessoa_meio_contato($PESSOA_CONTROLE, $PESSOA_CONTATO_CONTROLE, $TIPOMEIOCONTATO_CONTROLE, $VALOR, $STATUS) {

    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);

    $c_query = "INSERT INTO PESSOA_MEIOCONTATO SET                        ";
    $c_query .= " PESSOA_CONTROLE          = '$PESSOA_CONTROLE',          ";
    $c_query .= " PESSOA_CONTATO_CONTROLE  = '$PESSOA_CONTATO_CONTROLE',  ";
    $c_query .= " TIPOMEIOCONTATO_CONTROLE = '$TIPOMEIOCONTATO_CONTROLE', ";
    $c_query .= " VALOR                    = '$VALOR',                    ";
    $c_query .= " STATUS                   = '$STATUS'                    ";
    $res = mysql_query($c_query,$con);
    if($res){
        return mysql_insert_id($con);
    }
}

/**
 * Funcao para inserir dados na tabela NF
 */   
function insert_nota_fiscal() {
    
    return $GLOBALS['funcoes']->nova_nota( 2, 0, 0 );    
}

/**
 * Funcao para inserir dados na tabela NFITEM
 */   
function insert_nota_fiscal_item($NF_CONTROLE, $PRODUTO_CONTROLE, $QUANTIDADE, $VALOR_TOTAL, $STATUS) {

    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);

    // PRECOS
    $query_preco    = "SELECT PP.PRODUTOPRECO_CONTROLE, PP.PRECO, PP.UNIDADE_CONTROLE AS UNIDADE, PR.NOME, PC.CODIGO ";
    $query_preco    .= " FROM PRODUTOPRECO PP ";
    $query_preco    .= " INNER JOIN PRODUTO       PR ON PR.PRODUTO_CONTROLE = PP.PRODUTO_CONTROLE ";
    $query_preco    .= " INNER JOIN PRODUTOCODIGO PC ON PC.PRODUTO_CONTROLE = PR.PRODUTO_CONTROLE ";
    $query_preco    .= " WHERE PP.PRODUTO_CONTROLE = '$PRODUTO_CONTROLE' AND PP.TIPO = 1 LIMIT 1  ";
    $RecordsetPreco = mysql_query($query_preco, $con) or die(mysql_error());
    $rowPreco       = mysql_fetch_assoc($RecordsetPreco);
    $PRODUTOPRECO_CONTROLE = $rowPreco['PRODUTOPRECO_CONTROLE'];
    $NOME                  = $rowPreco['NOME'                 ];
    $PRECO                 = $rowPreco['PRECO'                ];
    $PRODUTOCODIGO         = $rowPreco['CODIGO'               ];
    $UNIDADE               = $rowPreco['UNIDADE'              ];
    $VALOR_TOTAL           = $VALOR_TOTAL / 100; 
    
    $c_query = "INSERT INTO NFITEM SET                              ";
    $c_query .= " NF_CONTROLE           = '$NF_CONTROLE',           ";
    $c_query .= " PRODUTO_CONTROLE      = '$PRODUTO_CONTROLE',      ";
    $c_query .= " QUANTIDADE            = '$QUANTIDADE',            ";
    $c_query .= " TABELAPRECO_CONTROLE  = '1',                      ";
    $c_query .= " PRODUTOPRECO_CONTROLE = '$PRODUTOPRECO_CONTROLE', ";
    $c_query .= " PRODUTONOME           = '$NOME',                  ";
    $c_query .= " VALORUNITARIODIGITADO = '$PRECO',                 ";
    $c_query .= " VALORTOTAL            = '$VALOR_TOTAL',           ";
    $c_query .= " PRODUTOCODIGO         = '$PRODUTOCODIGO',         ";
    $c_query .= " UNIDADE_CONTROLE      = '$UNIDADE',               ";
    $c_query .= " STATUS                = '$STATUS'                 ";
    $res = mysql_query($c_query,$con);
    if($res){
        return mysql_insert_id($con);
    }
}

/**
 * Funcao para inserir dados na tabela NFENDERECOENTREGA
 */   
function insert_nota_fiscal_endereco($NF_CONTROLE, $DOCUMENTO, $LOGRADOURO, $LOGRADOURONUMERO, $LOGRADOUROCOMPLEMENTO, $BAIRRO, $MUNICIPIONOME, $UF, $STATUS) {
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $c_query = "INSERT INTO NFENDERECOENTREGA SET                   ";
    $c_query .= " NF_CONTROLE           = '$NF_CONTROLE',           ";
    $c_query .= " DOCUMENTO             = '$DOCUMENTO',             ";
    $c_query .= " LOGRADOURO            = '$LOGRADOURO',            ";
    $c_query .= " LOGRADOURONUMERO      = '$LOGRADOURONUMERO',      ";
    $c_query .= " LOGRADOUROCOMPLEMENTO = '$LOGRADOUROCOMPLEMENTO', ";
    $c_query .= " BAIRRO                = '$BAIRRO',                ";
    $c_query .= " MUNICIPIONOME         = '$MUNICIPIONOME',         ";
    $c_query .= " UF                    = '$UF',                    ";
    $c_query .= " STATUS                = '$STATUS'                 ";
    $res = mysql_query($c_query,$con);
    if($res){
        return mysql_insert_id($con);
    }
}

/**
 * Atualiza os dados da nota fiscal
 * @return type
 */
function update_nf($NF_CONTROLE, $PESSOA_CONTROLE = NULL, $VALOR = 0, $FRETEVALOR = NULL, $CONDICAOFATURAMENTO_CONTROLE, $STATUS){
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $VALORNF = $VALOR + $FRETEVALOR;
    $c_query = "UPDATE NF SET ";
    # 2 parcelado, 8 a vista
    $PAGAMENTO = $CONDICAOFATURAMENTO_CONTROLE > 0 ? 2 : 8;
    if(!is_null($PESSOA_CONTROLE)){
        $c_query .= " PESSOA_CONTROLE   = '$PESSOA_CONTROLE',                  ";
    }
    $c_query .= " FRETEVALOR                   = '".$FRETEVALOR."',            ";
    $c_query .= " VALORNF                      = '".$VALORNF."',               ";
    $c_query .= " STATUS                       = '".define_status($STATUS)."', ";
    $c_query .= " CONDICAOFATURAMENTO_CONTROLE = '".$PAGAMENTO."'              ";
    $c_query .= " WHERE NF_CONTROLE = '$NF_CONTROLE'                           ";
    $res = mysql_query($c_query,$con);
    return $res;
    
}

/**
 * Atualiza o status do pedido registrado na tabela HUB2B_PEDIDO
 * @param type $NF_CONTROLE
 * @param type $STATUS
 * @return type
 */
function update_hub2b($NF_CONTROLE, $STATUS){
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $c_query = "UPDATE HUB2B_PEDIDO SET ";
    $c_query .= " STATUS            = '".define_status($STATUS)."' ";
    $c_query .= " WHERE NF_CONTROLE = '$NF_CONTROLE'               ";
    $res = mysql_query($c_query,$con);
    return $res;
    
}

//----------------------------------------------------------------------
// Algoritimo que implementa a logica da aplicacao
//----------------------------------------------------------------------

function postApi($url = NULL, $service = NULL, $options = NULL, $json){
    
    # Variaveis de configuracao
    $sandbox = TRUE;
    $id      = ($sandbox)         ? "90080"                : "";
    $auth    = ($sandbox)         ? "YXRlbmRlc21hcnQ6cHdk" : "";
    $url     = !is_null($url)     ? $url                   : "http://eb-api-sandbox.plataformahub.com.br/RestServiceImpl.svc";
    $service = !is_null($service) ? $service               : die("Erro: Informe um service!");
    
    # Caso tenha informacao, monta parametros para a url
    # exemplo: ?status=1&offset=0...
    if(is_array($options)){
        if(count($options) > 0){
            $string = "?";
            foreach($options as $key => $value):
                $string .= "$key=$value&";
            endforeach;
            $options = substr($string, 0, -1);
        }else{
            $options = NULL;
        }
    }
    
    # Monta o cabecalho
    $sku_options = array(
        'http' => array(
            'method' => 'POST',
            'header' => "Content-type: application/json\r\n"
            . "charset: UTF-8\r\n"
            . "User-Agent: PHP\r\n"
            . "Auth: {$auth}\r\n"
            . "Connection: Connection: Keep-Alive\r\n"
            . "Content-Length: " . strlen($json) . "\r\n",
            'content' => $json
        )
    );
    
    # Executa a chamada e realiza o retorno convertendo para objeto
    $xml = json_decode(file_get_contents("{$url}/{$service}/{$id}{$options}", false, stream_context_create($sku_options)));
    $doc = new DOMDocument();
    return $doc->loadXML($xml);
 
}

/**
 * Funcao que gera a chamada a API
 * @param type $url
 * @param type $service
 * @param type $options
 * @return type
 */
function requestApi($url = NULL, $service = NULL, $options = array()){
    
    # Variaveis de configuracao
    $sandbox = TRUE;
    $id      = ($sandbox)         ? "90080"                : "";
    $auth    = ($sandbox)         ? "YXRlbmRlc21hcnQ6cHdk" : "";
    $url     = !is_null($url)     ? $url                   : "http://eb-api-sandbox.plataformahub.com.br/RestServiceImpl.svc";
    $service = !is_null($service) ? $service               : die("Erro: Informe um service!");
    
    # Caso tenha informacao, monta parametros para a url
    # exemplo: ?status=1&offset=0...
    if(count($options) > 0){
        $string = "?";
        foreach($options as $key => $value):
            $string .= "$key=$value&";
        endforeach;
        $options = substr($string, 0, -1);
    }else{
        $options = NULL;
    }
    
    # Monta o cabecalho
    $sku_options = array(
        'http' => array(
            'method' => 'GET',
            'header' => "Content-type: application/json\r\n"
            . "charset: UTF-8\r\n"
            . "User-Agent: PHP\r\n"
            . "Auth: {$auth}\r\n"
            . "Connection: Connection: Keep-Alive\r\n"
        )
    );
    
    # Executa a chamada e realiza o retorno convertendo para objeto
    return json_decode(file_get_contents("{$url}/{$service}/{$id}{$options}", false, stream_context_create($sku_options)));
    
}

$pedidos = array();

# IDs dos pedidos registrados no Hub2b
$skus_rs = requestApi(NULL, "listordersid", array( "status" => "payment-pending,payment-approved,order-accepted", "offset" => 0, "limit" => 50 ));

    # Se retornou pedidos
    if($skus_rs->data != ""){
        
        # Se contem uma lista de pedidos
        if($skus_rs->data->list != "" && count($skus_rs->data->list) > 0){
            
           foreach ($skus_rs->data->list as $value):
           
                # Resgata o pedido pela id
                $pedidos[] = requestApi(NULL, "order", array( "order" => $value->orderId ));
                
           endforeach;
            
        }
        
    }


// Registra e atualiza os dados do pedido    
foreach($pedidos as $pedido):
    
    $consulta = select_pedido_hub2b($pedido->data->orderId);

    
    if(count($consulta['NF_CONTROLE']) > 0) {
        
        # update status nota
        $nf = $consulta['NF_CONTROLE'];
        update_nf( $nf, NULL, NULL, NULL, $pedido->data->paymentData->transactions[0]->payments[0]->installments, $pedido->data->status );
        update_hub2b( $nf, $pedido->data->status );
        
            $CFOP = select_nf_cfop($nf);

            # update codigo de rastreio
            $str_mkt = NULL;
            if(strlen($CFOP['RASTREIOPEDIDO']) > 1){
                
                $str_mkt .= '{
                            "type": "Output",
                            "invoiceNumber": "'. $CFOP['NFCODIGO'] .'",
                            "trackingNumber": "'. $CFOP['RASTREIOPEDIDO'] .'",
                            "invoiceValue": '. $CFOP['VALORNF'] * 100 .',
                            "invoiceKey": "'. $CFOP['CHAVENFE'] .'",
                            "invoiceSeries": "'. $CFOP['SERIENF'] .'",
                            "cfop": "'. $CFOP['CODIGO'] .'"
                          }';

                $json_mkt =  '['.$str_mkt.']';
                postApi(NULL, 'invoiceorder', NULL, $json_mkt);
                
            }
            
    }else{
        
        # Registra pedido na HUB2B_PEDIDO
        $rst = insert_pedido_hub2b($pedido->data->orderId, $pedido->data->status, $pedido->data->salesChannel);
        
        if($rst){
            
            # Verirfica se ja existe cadastro da PESSOA
            $pe = consulta_pessoa($pedido->data->clientProfileData->document);           
            
            if($pe == ''){
                
                # Se nao existe, registra PESSOA
                $pe = insert_pessoa(
                    $pedido->data->clientProfileData->firstName . " " .
                    $pedido->data->clientProfileData->lastName,
                    $pedido->data->clientProfileData->tradeName,
                    $pedido->data->clientProfileData->document
                    );
                
            }
            
            # Verifica se existe PESSOA_CONTATO
            $pc = consulta_pessoa_contato($pe);
            
            if($pc == ''){
                
                # Se nao existe, registra PESSOA_CONTATO
                $pc = insert_pessoa_contato($pe,
                        '1',
                        $pedido->data->clientProfileData->tradeName != "" ?
                            $pedido->data->clientProfileData->tradeName :
                            $pedido->data->clientProfileData->firstName,
                        '1'
                        );
                
            }
            
            $pm = consulta_pessoa_meio_contato( $pe, $pc, "2", $pedido->data->clientProfileData->email, "1" );
            
            if($pm == ''){
                
                # Registra PESSOA_MEIO_CONTATO [ 2 = e-mail   ]
                $pm = insert_pessoa_meio_contato( $pe, $pc, "2", $pedido->data->clientProfileData->email, "1" );
                
            }
            
            $pm = consulta_pessoa_meio_contato( $pe, $pc, "1", $pedido->data->clientProfileData->phone, "1" );
            
            if($pm == ''){
                
                # Registra PESSOA_MEIO_CONTATO [ 1 = telefone ]
                $pm = insert_pessoa_meio_contato( $pe, $pc, "1", $pedido->data->clientProfileData->phone, "1" );
                
            }
            
            # Registra NF
            $nf = insert_nota_fiscal();
            
            # Registra NFITEM
            foreach($pedido->data->items as $item):
                
               $ni = insert_nota_fiscal_item(
                        $nf,
                        $item->id,
                        $item->quantity,
                        $item->sellingPrice,
                        "1"
                    );
            
            endforeach;
            
            # Atualiza Hub2b_pedido passando o NF_CONTROLE
            inserir_nf_pedido_hub2b($pedido->data->orderId, $nf);
            
            # Endereco de entrega
            $ed = insert_nota_fiscal_endereco( $nf,
                    $pedido->data->clientProfileData->document,
                    $pedido->data->shippingData->address->street,
                    $pedido->data->shippingData->address->number,
                    $pedido->data->shippingData->address->complement,
                    $pedido->data->shippingData->address->neighborhood,
                    $pedido->data->shippingData->address->city,
                    $pedido->data->shippingData->address->state,
                    "1"
                    );
            
            # registra destinatario
            $funcoes->novo_destinatario( $nf, $pe );
            
            # update nota
            update_nf(
                    $nf,
                    $pe,
                    number_format( $pedido->data->value / 100, 2 ), # Valor com taxas,
                    number_format( $pedido->data->shippingAmount / 100, 2 ), # Frete,
                    $pedido->data->paymentData->transactions[0]->payments[0]->installments, # CONDICAOFATURAMENTO_CONTROLE,
                    $pedido->data->status );
        }
        
    }
    
endforeach;
 
    
echo "<pre>";
print_r($pedidos);
echo "</pre>";
die('FIM');

?>