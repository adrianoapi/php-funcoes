<?php


session_start();
require_once('../Connections/hhsystem.php');
include('../admin/restrito.php');
require_once('../../hhsystem/funcoes/funcoes.php');
require_once('../admin/geral.php');
include('../admin/config.php');
require_once('../../hhsystem/funcoes/Sajax.php');

//======================================================================
// ALGORITIMO PARA SINCRONIZACAO DE PRODUTOS NO HUB2B
//======================================================================


//-----------------------------------------------------
// Funcoes de auxilio de tratamento das variaveis
//-----------------------------------------------------
$iPagina = !isset($_GET['pagina']) ? 1  : $_GET['pagina']; 
$nome    = !isset($_GET['nome'  ]) ? '' : $_GET['nome'  ];
$codigo  = !isset($_GET['codigo']) ? '' : $_GET['codigo']; 

$total_reg = 50;
$inicio = $iPagina - 1; 
$inicio = $inicio * $total_reg;


if (isset($_GET['ordem'])) {
  $campo_ordem = $_GET['ordem'];
  $by = ($campo_ordem != 'PRODUTO_CONTROLE' ? 'ASC' : 'DESC');
} else {
  $by = 'DESC';
  $campo_ordem = 'PRODUTO_CONTROLE';
}

/**
 * Limpa os textos para criar o JSON
 * @param type $string
 * @return type
 */
function trata_texto($string){
    return trim(strtr($string, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC"));
}

/**
 * Remove quebras de linha e caracteres especiais
 * @param type $string
 * @return type
 */
function removeLn($string){
    $texto = isset($string) ? $string : '';
    $texto = str_replace('"',     "&quot;",  $texto);
    $texto = str_replace('„',     "&bdquo;", $texto);
    $texto = str_replace("¨",     "&uml;",   $texto);
    $texto = str_replace("'",     "&sbquo;", $texto);
    $texto = str_replace("\n",    " ",       $texto);  
    $texto = str_replace("\r",    " ",       $texto);
    $texto = preg_replace('/\s/', ' ',       $texto);
    return $texto;
}


//-----------------------------------------------------
// Funcoes especificas do algoritimo
//-----------------------------------------------------

/*
 * Fluxo do Algoritimo
 * 
 * 1. Seleciona todos os dados referente aos produtos
 * 2. Se TRUE, faz validacao de dados e quantidade
 * 3. Sincroniza os produtos
 * 4. Sincroniza os marketplaces e precos dos produtos
 * 5. Sincroniza as categorias
 * 
 */


/**
 * Funcao que gera o envio ah API
 * @param type $url
 * @param type $service
 * @param type $options
 * @return type
 */
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
    return json_decode(file_get_contents("{$url}/{$service}/{$id}{$options}", false, stream_context_create($sku_options)));
 
}

# Query para realizar a aginação
mysql_select_db($database_hhsystem, $hhsystem);
$query_RecordsetResumoTotal = "SELECT BP.*,"
                                  . " P.NOME,"
                                  . " P.ESTOQUE_UNIDADE_CONTROLE";
$query_RecordsetResumoTotal .= " FROM HUB2B_PRODUTO AS BP ";
$query_RecordsetResumoTotal .= " LEFT JOIN PRODUTO   P ON BP.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
$query_RecordsetResumoTotal .= " WHERE BP.HUB2B_PRODUTO_CONTROLE > 0 ";
if ($nome <> ''){
$query_RecordsetResumoTotal .= "AND P.NOME LIKE '%$nome%' " ;
}
if ($codigo <> ''){
$query_RecordsetResumoTotal .= "AND BP.HUB2B_PRODUTO_CONTROLE = '$codigo' " ;
}
$RecordsetResumoTotal = mysql_query($query_RecordsetResumoTotal, $hhsystem) or die(mysql_error());
$row_RecordsetResumoTotal = mysql_fetch_assoc($RecordsetResumoTotal);
$totalRows_RecordsetResumoTotal = mysql_num_rows($RecordsetResumoTotal);

# Query para realizar a listagem
$query_RecordsetResumo = "SELECT BP.*,"
                             . " P.NOME,"
                             . " PC.CODIGO AS VARIANTE_CODIGO,"
                             . " TD.NOME AS DETALHE_NOME,"
                             . " PLE.PRODUTOLOCALESTOQUE_CONTROLE, "
                             . " PLE.LOCALESTOQUE_CONTROLE, "
                             . " PLE.SALDO AS ESTOQUE_UNIDADE_CONTROLE ";
$query_RecordsetResumo .= " FROM HUB2B_PRODUTO AS BP ";
$query_RecordsetResumo .= " LEFT JOIN PRODUTOLOCALESTOQUE        PLE ON BP.PRODUTO_CONTROLE = PLE.PRODUTO_CONTROLE AND BP.VARIANTE_CONTROLE = PLE.PRODUTOVARIANTE_CONTROLE ";
$query_RecordsetResumo .= " LEFT JOIN PRODUTO                    P   ON BP.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
$query_RecordsetResumo .= " LEFT JOIN PRODUTOCODIGO              PC  ON PC.CODIGO = BP.CODIGO";
$query_RecordsetResumo .= " LEFT JOIN PRODUTOVARIANTE            PVC ON PVC.PRODUTOVARIANTE_CONTROLE =  BP.VARIANTE_CONTROLE ";
$query_RecordsetResumo .= " LEFT JOIN PRODUTOVARIANTEDETALHE     PVD ON PVD.PRODUTOVARIANTE_CONTROLE =  PVC.PRODUTOVARIANTE_CONTROLE ";
$query_RecordsetResumo .= " LEFT JOIN TIPODETALHE                TD  ON TD.TIPODETALHE_CONTROLE =  PVD.TIPODETALHE_CONTROLE ";
$query_RecordsetResumo .= " WHERE BP.HUB2B_PRODUTO_CONTROLE > 0 ";
if ($nome <> ''){
    $query_RecordsetResumo .= "AND P.NOME LIKE '%$nome%' ";
}
if ($codigo <> ''){
    $query_RecordsetResumo .= "AND P.PRODUTO_CONTROLE = '$codigo' ";
}
$query_RecordsetResumo .= "GROUP BY  BP.HUB2B_PRODUTO_CONTROLE ";
$query_RecordsetResumo .= "ORDER BY P.{$campo_ordem} {$by} ";
$query_RecordsetResumo .= "LIMIT $inicio,$total_reg ";

$RecordsetResumo = mysql_query($query_RecordsetResumo, $hhsystem) or die(mysql_error());
$row_RecordsetResumo = mysql_fetch_assoc($RecordsetResumo);
$totalRows_RecordsetResumo = mysql_num_rows($RecordsetResumo);


function excluir_cadastro($CONTROLE) {
	
	$con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);

	$c_query  = " DELETE FROM HUB2B_PRODUTO ";
	$c_query .= " WHERE HUB2B_PRODUTO_CONTROLE = '$CONTROLE' ";
	$res = mysql_query($c_query,$con);
	
	return '';

}

/*
 * Lista marketplace referente ao hub2b produto
 */
function get_porduto_marketplace($PRODUTO_CONTROLE){
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $query  = "SELECT * FROM HUB2B_PRODUTO_MARKETPLACE             ";
    $query .= " WHERE HUB2B_PRODUTO_CONTROLE = '$PRODUTO_CONTROLE' ";
    $RecordsetProduto = mysql_query($query, $con) or die(mysql_error());
    
    $arr = array();
    do{
        if($rowProduto['HUB2B_MARKETPLACE_CONTROLE'] > 0){
            array_push($arr, $rowProduto['HUB2B_MARKETPLACE_CONTROLE']);
        }
    }while($rowProduto = mysql_fetch_assoc($RecordsetProduto));
    
    return $arr;
}

/**
 * Sincronizacao em massa de produtos
 * @param type $value
 * @return type
 */
function sincronizarMassa($value, $MARKETPLACE_STATUS)
{
    $debug  = FALSE;
    $array  = explode(",", $value);
    $result = array();
    $config = array();
    $config['quantidade'] = FALSE;
    $config['erros'     ] = TRUE;
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    foreach($array as $value):
        
        $query_RecordsetProduto = "SELECT BP.*,"
                                  . " P.NOME,"
                                  . " PLE.SALDO AS QUANTIDADE,"
                                  . " P.DESCRICAO_LOJA,"
                                  . " P.ALTURA,"
                                  . " P.LARGURA,"
                                  . " P.COMPRIMENTO,"
                                  . " P.PESO_LIQUIDO,"
                                  . " P.NCM,"
                                  . " PC.CODIGO AS CODIGO_BARRA,"
                                  . " MA.NOME AS NOME_MARCA,"
                                  . " PG.GRUPO_CONTROLE,"
                                  . " PLE.PRODUTOLOCALESTOQUE_CONTROLE, "
                                  . " PLE.LOCALESTOQUE_CONTROLE, "
                                  . " PLE.SALDO AS ESTOQUE_UNIDADE_CONTROLE, "
                                  . " LE.LOCALESTOQUE_CONTROLE, "
                                  . " LE.NOME AS LOCALESTOQUE_NOME, "
                                  . " GR.NOME AS GRUPO_NOME";
        $query_RecordsetProduto .= " FROM HUB2B_PRODUTO AS BP ";
        $query_RecordsetProduto .= " LEFT JOIN PRODUTO             P   ON BP.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
        $query_RecordsetProduto .= " LEFT JOIN PRODUTOCODIGO       PC  ON PC.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
        $query_RecordsetProduto .= " LEFT JOIN PRODUTOLOCALESTOQUE PLE ON PC.PRODUTO_CONTROLE = PLE.PRODUTO_CONTROLE AND PC.PRODUTOVARIANTE_CONTROLE = PLE.PRODUTOVARIANTE_CONTROLE ";
        $query_RecordsetProduto .= " LEFT JOIN LOCALESTOQUE        LE  ON PLE.LOCALESTOQUE_CONTROLE = LE.LOCALESTOQUE_CONTROLE ";
        $query_RecordsetProduto .= " LEFT JOIN MARCA               MA  ON MA.MARCA_CONTROLE   = P.MARCA_CONTROLE ";
        $query_RecordsetProduto .= " LEFT JOIN PRODUTOGRUPO        PG  ON PG.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
        $query_RecordsetProduto .= " LEFT JOIN GRUPO               GR  ON GR.GRUPO_CONTROLE   = PG.GRUPO_CONTROLE ";
        $query_RecordsetProduto .= " WHERE BP.HUB2B_PRODUTO_CONTROLE =  '$value' ";
        $RecordsetProduto = mysql_query($query_RecordsetProduto, $con) or die(mysql_error());
        $row_RecordsetProduto = mysql_fetch_assoc($RecordsetProduto);
        $totalRows_RecordsetProduto = mysql_num_rows($RecordsetProduto);
        $result[] = $row_RecordsetProduto;
    
    endforeach;
    
    // Define o caminho da imagem
    $caminho = $_SESSION['CAMINHO_FOTO'];
    $caminho.  'userfiles/' . $PRODUTO_CONTROLE.  '/sistema/mini.jpg';
    
    # Monta o JSON para envio
    $string           = '';
    $count            = 0;
    $arr_curso        = array();
    $INDICE_CONTROLE  = array();
    $INDICE_CATEGORIA = array();
    $INDICE_ESTOQUE   = array();
    $INDICE_ARMAZEM   = array();
    $INDICE_PRECO     = array();
    $MARKETPLACE      = array();
    foreach ($result as $value):
        
        $INDICE_CONTROLE[$count] = $value['HUB2B_PRODUTO_CONTROLE'];
        $INDICE_CATEGORIA[$value['GRUPO_CONTROLE']] = $value['GRUPO_NOME'];
        
        # ESTOQUE HUB2B
        $INDICE_ESTOQUE[$value['PRODUTOLOCALESTOQUE_CONTROLE']] = array(
            "ARMAZEM"          => $value['LOCALESTOQUE_CONTROLE'],
            "QUANTIDADE"       => $value['QUANTIDADE'],
            "PRODUTO_CONTROLE" => $value['PRODUTO_CONTROLE'],
            "MANUSEIO"         => 0
            );
        
        # ARMAZEM HUB2B
        $INDICE_ARMAZEM[$value['LOCALESTOQUE_CONTROLE']] = array(
            "MANUSEIO" => 0,
            "NOME"     => $value['LOCALESTOQUE_NOME'],
            "CEP"      => "90234999"
            );
    
        $HUB2B_PRODUTO_CONTROLE = $value['HUB2B_PRODUTO_CONTROLE'      ];
        $PRODUTO_CONTROLE       = $value['PRODUTO_CONTROLE'            ];
        $NCM                    = $value['NCM'                         ];
        $CODIGO_BARRA           = $value['CODIGO_BARRA'                ];
        $CODIGO                 = $value['CODIGO'                      ];
        $DESCRICAO_LOJA         = $value['DESCRICAO_LOJA'              ];
        $NOME                   = $value['NOME'                        ];
        $GRUPO_NOME             = $value['GRUPO_NOME'                  ];
        $GRUPO_NOME_FORM        = $value['GRUPO_NOME'                  ];
        $ALTURA                 = $value['ALTURA'                      ];
        $LARGURA                = $value['LARGURA'                     ];
        $COMPRIMENTO            = $value['COMPRIMENTO'                 ];
        $PESO_LIQUIDO           = $value['PESO_LIQUIDO'                ];
        $LINK                   = $value['LINK'                        ];
        $MARCA                  = $value['NOME_MARCA'                  ];
        $GRUPO_CONTROLE         = $value['GRUPO_CONTROLE'              ];
        $QUANTIDADE             = $value['QUANTIDADE'                  ];
        $ESTOQUE_CONTROLE       = $value['PRODUTOLOCALESTOQUE_CONTROLE'];
    
        // IMAGENS
        $query_imagem = "SELECT NOME_IMAGEM FROM PRODUTOIMAGEM WHERE PRODUTO_CONTROLE = '$PRODUTO_CONTROLE' LIMIT 1";
        $RecordsetImg = mysql_query($query_imagem, $con) or die(mysql_error());
        $rowImg       = mysql_fetch_assoc($RecordsetImg);

        // PRECOS
        $query_preco    = "SELECT PRECO FROM PRODUTOPRECO WHERE PRODUTO_CONTROLE = '$PRODUTO_CONTROLE' AND TIPO = 1 LIMIT 1";
        $RecordsetPreco = mysql_query($query_preco, $con) or die(mysql_error());
        $rowPreco       = mysql_fetch_assoc($RecordsetPreco);

        // COMPOSICAO
        $query_comp     = "SELECT PC.*, PR.NOME";
        $query_comp    .=  " FROM PRODUTOCOMPOSICAO AS PC ";
        $query_comp    .=  " LEFT JOIN PRODUTO      AS PR  ON PC.PRODUTO_CONTROLE = PR.PRODUTO_CONTROLE";
        $query_comp    .=  " WHERE PC.PRODUTO_CONTROLE = '$PRODUTO_CONTROLE'";
        $RecordsetComp  = mysql_query($query_comp, $con) or die(mysql_error());
        $totalRows_RecordsetComp = mysql_num_rows($RecordsetComp);
        
        // MARKETPLACE
        $MARKETPLACE[$PRODUTO_CONTROLE ] = get_porduto_marketplace($HUB2B_PRODUTO_CONTROLE);
        
        $INDICE_PRECO[$PRODUTO_CONTROLE] = substr($rowPreco['PRECO'], 0, -2);
        
        // Validar erros no produto
        $msg_erro = array();
        if( empty($NOME)                   ){ $msg_erro[] = trata_texto("Nome inválido: ". $NOME                              );}
        if( !is_numeric($PRODUTO_CONTROLE) ){ $msg_erro[] = trata_texto("ID inválido: ". $PRODUTO_CONTROLE                    );}
        if( empty($GRUPO_CONTROLE)         ){ $msg_erro[] = trata_texto("Categoria inválida: informe uma categoria"           );}
        if( $ALTURA <= 0                   ){ $msg_erro[] = trata_texto("Altura inválida: ". $ALTURA                          );}
        if( $LARGURA <= 0                  ){ $msg_erro[] = trata_texto("Largura inválida: ". $LARGURA                        );}
        if( $COMPRIMENTO <= 0              ){ $msg_erro[] = trata_texto("Comprimento inválido: ". $COMPRIMENTO                );}
        if( $PESO_LIQUIDO <= 0             ){ $msg_erro[] = trata_texto("Peso inválido: ". $PESO_LIQUIDO                      );}
        if( empty($LINK)                   ){ $msg_erro[] = trata_texto("Link inválido: ". $LINK                              );}
        if( empty($rowImg['NOME_IMAGEM'])  ){ $msg_erro[] = trata_texto("Produto sem imagem: é preciso ter imagem"            );}
        if( count($rowPreco) < 1           ){ $msg_erro[] = trata_texto("Produto sem preço: é preciso ao menos 1 valor"       );}
        if( $rowPreco['PRECO'] < 1         ){ $msg_erro[] = trata_texto("O preço do proudto deve ser maior que 0: ".substr($rowPreco['PRECO'], 0, -2));}
        if( $QUANTIDADE < 1                ){ $msg_erro[] = trata_texto("Produto sem quantidade: é preciso ao menos 1 unidade");}
        if( $_SESSION['LOJA_VIRTUAL'] < 1  ){ $msg_erro[] = trata_texto("O sistema precisa ser do tipo loja virtual"          );}
        
        if(count($msg_erro) > 0){
            $arr_curso[] = array(
                'titulo' => trata_texto($NOME),
                'erro'   => $msg_erro
            );
        }
        
        // Campos Opcionais
        $GRUPO_NOME = empty($GRUPO_NOME) ? NULL : '"Ambiente": "'.$GRUPO_NOME.'",';
        $ITENS_INC  = empty($str_itens)  ? NULL : '"Itens Inclusos": "'.$str_itens.'",';
        
        // Obrigatorio
        $MARCA = empty($MARCA) ? "none"     : $MARCA;
        $NCM   = empty($NCM)   ? "00000000" : $NCM;
        
        $string .= '{
                "sku": "'. $PRODUTO_CONTROLE .'",
                "parentSKU": "",
                "ean13": "'. $CODIGO_BARRA .'",
                "warrantyMonths": "3",
                "handlingTime": "2",
                "stock": "'. substr($QUANTIDADE, 0, -4) .'",
                "weightKg": "'.$PESO_LIQUIDO.'",
                "url": "'.$LINK.'",
                "sourceId": "'.$PRODUTO_CONTROLE.'",
                "category": "'.$GRUPO_CONTROLE.'",
                "name": "'.$NOME.'",
                "description": "'.removeLn($DESCRICAO_LOJA).'",
                "brand": "'.$MARCA.'",
                "ncm": "'. $NCM .'",
                "priceBase": "'.substr($rowPreco['PRECO'], 0, -2).'",
                "priceSale": "'.substr($rowPreco['PRECO'], 0, -2).'",
                "idProductType": "1",
                "idTypeCondition": "1",
                "shippingType": "me1",
                "images": [{"url": "'.$caminho.$rowImg['NOME_IMAGEM'].'"}],
                "specifications": [{
                        "name": "Garantia",
                        "value": "30 dias",
                        "type": 2
                }],
                "height_m": "'.$ALTURA.'",
                "width_m": "'.$LARGURA.'",
                "length_m": "'.$COMPRIMENTO.'"
            }';
        $count++;
        $string .= $count < count($result) ? ',' : '';
    endforeach;
    
    # Proteção para quantidade mínima de produtos
    if(($count < 20 || $count > 1000) && $config['quantidade'] == TRUE){
        $rst = json_encode(array('status' => false, 'msg' => "A quantidade deve ser de 20 a 1k!", 'options' => NULL));
        return $rst;
    }
    
    # Checa se houve erro nos dados do produto
    if(count($arr_curso) > 0 && $config['erros'] == TRUE){
        $rst = json_encode(array('status' => false, 'msg' => "Erro nos dados do(s) produto(s):", "options" => $arr_curso));
        return $rst;
    }
    
    /*
     * Sincroniza Produtos
     */
    $json   = trata_texto("[$string]");    
    $ws_ret = postApi(NULL, 'setsku', NULL, $json);
    
    /*
     * Sincroniza Marketplaces
     */
    if(count($MARKETPLACE) > 0){
        $str_mkt = NULL;
        foreach($MARKETPLACE as $KEY => $ITENS):
            
            foreach($ITENS as $value):
                $str_mkt .= '{
                            "itemId": "'. $KEY .'",
                            "salesChannel": "'. $value .'",
                            "price": '. $INDICE_PRECO[$KEY] .',
                            "listPrice": '. $INDICE_PRECO[$KEY] .',
                            "validFrom": "",
                            "validTo": ""
                          },';
            endforeach;
            
        endforeach;
        
        $json_mkt =  '['.substr($str_mkt, 0, -1).']';
        $action   = $MARKETPLACE_STATUS == 'relacionar' ? 'setprice' : 'removeproduct';
        $ws_mkt   = postApi(NULL, $action, NULL, $json_mkt);
    }
    # Fim marketplaces
    
    
    /*
     * Sincroniza catgorias
     */
    if(count($INDICE_CATEGORIA) > 0){
        
        $str_categoria = NULL;
        foreach($INDICE_CATEGORIA as $key => $value):
            $str_categoria .= '{
                                "code": "'. $key .'",
                                "parentCode": "",
                                "name": "' . trata_texto($value) . '"
                               },';
        endforeach;
        
        $json_categoria =  '['.substr($str_categoria, 0, -1).']';
        $ws_cat         = postApi(NULL, 'setcategory', NULL, $json_categoria);
    }
    # Fim categorias
    
    /*
     * Sincroniza estoque
     */
    if(count($INDICE_ESTOQUE) > 0){
        
        $str_estoque = NULL;
        foreach($INDICE_ESTOQUE as $key => $value):
            $str_estoque .= '{
                                "wareHouseId": "'. $value['ARMAZEM'] .'",
                                "itemId": "'. $value['PRODUTO_CONTROLE'] .'",
                                "quantity": "'. $value['QUANTIDADE'] .'",
                                "crossdocking": "'. $value['MANUSEIO'] .'"
                               },';
        endforeach;
        
        $json_estoque =  '['.substr($str_estoque, 0, -1).']';
        $ws_est       = postApi(NULL, 'setstock', NULL, $json_estoque);
    }
    # Fim estoque
    
    /*
     * Sincroniza armazem
     */
    if(count($INDICE_ARMAZEM) > 0){
        
        $str_armazem = NULL;
        foreach($INDICE_ARMAZEM as $key => $value):
            $str_armazem .= '{
                                "wareHouseId": "'. $key .'",
                                "crossdocking": "'. $value['MANUSEIO'] .'",
                                "name": "'. $value['NOME'] .'",
                                "zipCode": "'. $value['CEP'] .'"
                               },';
        endforeach;
        
        $json_armazem =  '['.substr($str_armazem, 0, -1).']';
        $ws_arm       = postApi(NULL, 'setwarehouse', NULL, $json_armazem);
    }
    # Fim armazem
    
    # Trata a resposta do produto
    # Verifica se o WS HUB2B retornou sem erro
    if($ws_ret->error == ""){
        
        foreach ($INDICE_CONTROLE as $value):
            $c_query  = "UPDATE HUB2B_PRODUTO SET                 ";
            $c_query .= " STATUS_CODE = '200'                     ";
            $c_query .= " WHERE HUB2B_PRODUTO_CONTROLE = {$value} ";
            $res = mysql_query($c_query,$con);
        endforeach;
        
        $rst = array('status' => true, 'msg' => 'Sincronizado com sucesso!');
    }else{
        $rst = array('status' => false,'msg' => 'Erro ao sincronizar!', 'error' => ($ws_ret));
    }
    print_r(json_encode($rst));
    
# Fim sincronizarMassa
}

$sajax_request_type = "GET"; //forma como os dados serao enviados
sajax_init(); //inicia o SAJAX
sajax_export("sincronizarMassa","excluir_cadastro"); // lista de funcoes a ser exportadas
sajax_handle_client_request();// serve instancias de clientes

$_SESSION['titulo'] = 'Cadastro de produto Hub2b';
include('../admin/header.php');

?>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css" rel="stylesheet" />
<style type="text/css">
    select {
        font-family: 'FontAwesome', 'sans-serif';
    }
</style>
<script language="javascript" src="hub2b.js?v=025"></script>
<script language="javascript"><!--
<?
sajax_show_javascript(); //gera o javascript
?>
//-->
</script>


<form action="" method="get" name="form2" id="form2">
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="400"><a class="fancybox fancybox.iframe" href="hub2b_detalhes.php"><img src="../images/bt_novo_cadastro.jpg" width="131" height="30" border="0" /></a></td>
      <td width="400" align="right"><span class="titulo_pagina"><a href="hub2b_pedidos.php">Pedidos</a></span></td>
      <td width="400" align="right"><span class="titulo_pagina">Cadastro de produtos Hub2b</span></td>
    </tr>
    <tr>
      <td height="15"></td>
      <td></td>
      <td></td>
    </tr>
  </table>
  
<table width="798" border="0" align="center" cellspacing="5" class="grade_dados">
  <tr>
    <td width="8%"  align="right" >ID Produto:</td>
    <td width="5%"  align="left"  ><input name="codigo" type="text" id="id" size="4" value="<?php echo $codigo;?>" /></td>
    <td width="7%"  align="right" >Nome:</td>
    <td width="16%" align="left"  ><input name="nome" type="text" id="nome" size="20" value="<?php echo $nome;?>" /></td>
    <td width="13%" align="right" >Ordenar por:</td>
    <td width="10%" align="left"  ><select name="ordem" id="ordem">
      <option value="PRODUTO_CONTROLE" <?php if (!(strcmp("PRODUTO_CONTROLE", $campo_ordem))) {echo "SELECTED";} ?>>C&oacute;digo</option>
      <option value="NOME" <?php if (!(strcmp("NOME", $campo_ordem))) {echo "SELECTED";} ?>>Nome</option>
    </select></td>
    <td width="14%" align="center"><input type="submit" value="Buscar" /></td>
  </tr> 
</table>
<br />
 
<?php 
if($totalRows_RecordsetResumo != 0 ){ ?>   
  <table width="800" border="0" cellspacing="1" cellpadding="3" align="center">   
    <tr bgcolor="#0066FF">
      <!--<td width="100" height="20" bgcolor="#0066FF" class="style6"><div align="left">C&oacute;digo</div></td>-->
      <td width="100" bgcolor="#0066FF" class="style6"><div align="left">ID Produto</div></td>
      <td width="100" bgcolor="#0066FF" class="style6"><div align="left">Código</div></td>
      <td width="100" bgcolor="#0066FF" class="style6"><div align="left">Qtd</div></td>
      <td width="560" class="style6"><div align="left">Nome</div></td>
      <td width="1%" align="center" class="titulo_grade"><input type="checkbox" onClick="check_all(this)" /></td>
      <td width="20" align="right" bgcolor="#0066FF" class="style6">&nbsp;</td>
      <td width="20" align="right" bgcolor="#0066FF" class="style6">&nbsp;</td>
      <td width="20" align="right" bgcolor="#0066FF" class="style6">&nbsp;</td>
    </tr>
    <?php 
      do { 
        ?>
        <tr id="<?php echo 'linha_2'.$ii?>" onMouseOut="cor_linha1('2','<?php echo $ii?>')" onMouseOver="cor_linha1('1','<?php echo $ii?>')"  bgcolor="#F2F2F2">
          <td><?php echo $row_RecordsetResumo['PRODUTO_CONTROLE']; ?></td>
          <td><?php echo isset($row_RecordsetResumo['VARIANTE_CODIGO']) ? $row_RecordsetResumo['VARIANTE_CODIGO'] : $row_RecordsetResumo['CODIGO']; ?></td>
          <td><?php echo $row_RecordsetResumo['ESTOQUE_UNIDADE_CONTROLE']; ?></td>
          <td align="left"><?php echo $row_RecordsetResumo['NOME']." ".$row_RecordsetResumo['DETALHE_NOME']; ?></td>
          <td align="center">
            <label id="checkSincronizar">
                <input type="checkbox" class="flat" name="exportar[]" value="<?php echo $row_RecordsetResumo['HUB2B_PRODUTO_CONTROLE']; ?>">
            </label>
          </td>
          <td align="center"><img src="../images/<?php echo $row_RecordsetResumo['STATUS_CODE'] != 200 ? '1.gif' : '2.gif'; ?>" width="16" height="16" border="0" title="Sincronizar produto"></td>
          <td align="center"><a class="fancybox fancybox.iframe" href="hub2b_detalhes.php?cmd=<?php echo $row_RecordsetResumo['HUB2B_PRODUTO_CONTROLE']; ?>"><img src="../images/b_browse.png" width="16" height="16" border="0" title="Alterar produto"></a></td>
          <td align="center"><img src="../images/delete.gif" width="16" height="15" onclick="if (!!menu_novo('<?php echo $_SESSION['usr_pode_banco'];?>','E','','')){ excluir_cadastro(<?php echo $row_RecordsetResumo['HUB2B_PRODUTO_CONTROLE']; ?>) ;}" style="cursor:pointer;" title="Excluir produto" /></td>
        </tr>
    <?php 
      $ii++;
      } while ($row_RecordsetResumo = mysql_fetch_assoc($RecordsetResumo)); 
      ?>
    <tr bgcolor="#F2F2F2">
        <td align="left">&nbsp; </td>
        <td align="left">&nbsp;</td>
        <td align="left">&nbsp;</td>
        <td align="right">
            
        <select name="MARKETPLACE_STATUS" id="MARKETPLACE_STATUS">
            <option value="relacionar">&#xf0c1; Relacionar</option>
            <option value="remover"   >&#xf127; Remover   </option>
        </select>
        </td>
        <td align="right" colspan="3">
            <input name="" id="btn-sincronizar" type="button" value="Sincronizar" onclick="sincronizarMassa()">
        </td>
        <td align="left">&nbsp;</td>
    </tr>
  </table>



<table width="800" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td align="right">

        <table width="0"  border="0" align="right" cellpadding="1" cellspacing="1">
            <tr>
                <td><font size="3">Página(s):</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
			
<?php
if ($iPagina > 1) { 
?>

    <td valign="bottom">
        <a href="?pagina=1&nome=<?php echo $nome;?>&codigo=<?php echo $codigo;?>"><img src='../images/bt_pag_primeiro.jpg' width="20" height="20" border='0' align="absbottom" ></a> 
    </td>
      <td valign="bottom">
        <a href="?pagina=<?php echo ($iPagina - 1) ?>&nome=<?php echo $nome;?>&codigo=<?php echo $codigo;?>"><img src='../images/bt_pag_anterior.jpg' width="20" height="20" border='0' align="absbottom" ></a> 
    </td>  

    <?php }

    $max_n_mostrados = 10;
    $TotalPages = ceil($totalRows_RecordsetResumoTotal/$total_reg);
    $intervalo = ceil($max_n_mostrados/2);
    $inicio = $iPagina - $intervalo;
    $final = $iPagina + $intervalo;


    if ($inicio < 1) { 
     $inicio = 1;
     $final = 10;
    }

    if ($final > $TotalPages) { 
    $final = $TotalPages;
    }

    for ($i = $inicio; $i <= $final; $i++) {

         if ($i == $iPagina) {?>
           <td valign="bottom"><span class='botao_pag_marcado'>&nbsp;<?php echo $i; ?>&nbsp;</span>&nbsp;</td>
    <?php }
         if ($i < $iPagina) {?>
           <td valign="bottom"><span class='botao_pag_nao_marcado'><a href="?pagina=<?php echo $i;?>&nome=<?php echo $nome;?>&codigo=<?php echo $codigo;?>">&nbsp;<?php echo $i; ?>&nbsp;</a></span>&nbsp;</td>
    <?php }

         if ($i > $iPagina) {?>
           <td valign="bottom"><span class='botao_pag_nao_marcado'><a href="?pagina=<?php echo $i;?>&nome=<?php echo $nome;?>&codigo=<?php echo $codigo;?>">&nbsp;<?php echo $i; ?>&nbsp;</a></span>&nbsp;</td>
    <?php }

    }


    if ( (int)$iPagina != $TotalPages ) {
    ?>
     <td valign="bottom">
        <a href="?pagina=<?php echo $iPagina+1; ?>&nome=<?php echo $nome;?>&codigo=<?php echo $codigo;?>"><img src='../images/bt_pag_proxima.jpg' border='0' align="absbottom" ></a> 
     </td>
      <td valign="bottom">
        <a href="?pagina=<?php echo $TotalPages; ?>&nome=<?php echo $nome;?>&codigo=<?php echo $codigo;?>"><img src='../images/bt_pag_ultima.jpg' border='0' align="absbottom" ></a> 
     </td>  
    <?php 
        } 
    ?>
            </tr>
        </table>    
   </td>
  </tr>
</table>

<?php } ?>
</form>



</body>
</html>
<?php
mysql_free_result($RecordsetResumo);
?>