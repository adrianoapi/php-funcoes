<?php


session_start();
require_once('../Connections/hhsystem.php');
include('../admin/restrito.php');
require_once('../../hhsystem/funcoes/funcoes.php');
require_once('../admin/geral.php');
include('../admin/config.php');
require_once('../../hhsystem/funcoes/Sajax.php');


/*
 * Fluxo do Algoritimo
 * 
 * 1. Atualiza a quantidade de tempo para prepara e define 30min como default;
 * 2. Faz a listagem de todos os pedidos no cupom caixa com limit de 10;
 * 
 */

# Define tempo sessao
$_SESSION['TEMPO_PREPARACAO'] = is_null($_SESSION['TEMPO_PREPARACAO']) ? 30 : $_SESSION['TEMPO_PREPARACAO'];

if($_POST){
    $_SESSION['TEMPO_PREPARACAO'] = $_POST['TEMPO_PREPARACAO'];
}

function dataMySql2BR($timestamp){
    $array = explode(" ", $timestamp);
    $date  = explode("-", $array[0]);
    return $date[2]."/".$date[1]."/".$date[0]." ".$array[1];
}

function data_hora(){
    return dataMySql2BR(date('Y-m-d H:i:s'));
}

function soNumero($number){
      return preg_replace("/[^0-9]/", "", $number);
}

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

function trata_texto($string){
    return trim(strtr($string, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC"));
}

function status($int){
    switch ($int) {
        case 0:
            return "Em preparação";
            break;
        case 1:
            return "Solicitar motoboy";
            break;
        case 2:
            return "Motoboy Solicitado";
            break;
        case 3:
            return "Entrega em curso";
            break;
        case 4:
            return "Entregue";
            break;
        case 5:
            return "Erro";
            break;
        default:
            break;
    }
}

function acrescenta_min($horario, $minutos){
    # Acrescenta o tempo de preparo
    $time = new DateTime($horario);
    $time->add(new DateInterval('PT' . $minutos . 'M'));
    return $stamp = $time->format('Y-m-d H:i:s');
}

function dif_hora($datatime2){
    # Padroniza e compara
    $datatime1 = date('Y-m-d H:i:s');
    $datatime1 = new DateTime($datatime1);
    $datatime2 = new DateTime($datatime2);
    return $datatime1 >= $datatime2 ? 2 : 1;
}

function dif_hora_draw($timestamp1, $timestamp2){
    # Padroniza e compara
    $to_time = strtotime($timestamp1);
    $from_time = strtotime($timestamp2);
    return round(abs($to_time - $from_time) / 60,2). " minuto(s)";
}

/**
 * Isola o cep e numero retornando-os em array
 * @param type $string
 * @return array
 */
function tratar_cep($string){

    $rst = explode(',', $string);
    $arr = array();
    
    if(count($rst) < 5){
        # Sem complemento
        $arr['LOGRADOURO' ] = $rst[0];
        $arr['NUMERO'     ] = $rst[1];
        $arr['COMPLEMENTO'] = NULL;
        $arr['BAIRRO'     ] = $rst[2];
        $arr['CEP'        ] = $rst[3];
    }else{
        # Com complemento
        $arr['LOGRADOURO' ] = $rst[0];
        $arr['NUMERO'     ] = $rst[1];
        $arr['COMPLEMENTO'] = $rst[2];
        $arr['BAIRRO'     ] = $rst[3];
        $arr['CEP'        ] = $rst[4];
    }
    
    return $arr;
}

/**
 * Define um ID de pagamento
 * Dinheiro   = 1
 * Maq Cartao = 2
 * Pago       = 1002
 */
function define_forma_pagamento($pgto){
    
    switch($pgto){
        case (strpos($pgto, 'Crédito') !== false) :
            return 2;
            break;
        case (strpos($pgto, 'Débito') !== false) :
            return 2;
            break;
        case (strpos($pgto, 'Dinheiro') !== false) :
            return 1;
            break;
        default:
            return 1002;
            break;
    }
    
}

function select_endereco_by_cep($cep){
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $query  = "SELECT PE.*, MU.NOME AS CIDADE, UF.NOME AS ESTADO FROM PESSOA_ENDERECO PE ";
    $query .= " INNER JOIN MUNICIPIO MU ON MU.MUNICIPIO_CONTROLE = PE.MUNICIPIO_CONTROLE ";
    $query .= " INNER JOIN UF           ON UF.UF_CONTROLE = PE.UF_CONTROLE ";
    $query .= " WHERE PE.CEP = '$cep' ";
    $query .= " LIMIT 1 ";
    $res2 = mysql_query($query,$con);
    return  mysql_fetch_assoc($res2);

}

function sendGo4You($controle, $status){
  
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $c_query = "SELECT CC.ENDERECO, CC.CPF_CNPJ_VALUE AS CPF, CC.VALORPRODUTOS, " .
                " PE.NOME, " .
                " CN.VALOR AS TROCO, " .
                " NU.NOME AS FORMA_PGTO " .
                " FROM CAIXACUPOM CC "; 
    $c_query .= " INNER JOIN PESSOA              PE ON PE.PESSOA_CONTROLE = CC.CLIENTE_CONTROLE ";
    $c_query .= " LEFT  JOIN CAIXACUPOMNUMERARIO CN ON CN.CAIXACUPOM_CONTROLE = CC.CAIXACUPOM_CONTROLE AND CN.OPERACAO = '2' ";
    $c_query .= " LEFT  JOIN NUMERARIO           NU ON NU.NUMERARIO_CONTROLE =  CN.NUMERARIO_CONTROLE ";
    $c_query .= " WHERE CC.CAIXACUPOM_CONTROLE = '$controle' ";
    $c_query .= " LIMIT 1 ";
    $res = mysql_query($c_query,$con);
    $row_RecordsetResumo = mysql_fetch_assoc($res);
    $totalRows_RecordsetResumo = mysql_num_rows($res);
    
    # Cria array para endereco
    $logradouro  = NULL;
    $numero      = NULL;
    $cep         = NULL;
    $bairro      = NULL;
    $cidade      = 'Sao Paulo';
    $estado      = 'SP';
    $complemento = NULL;
    
    # Localiza cep e endereco
    if(!empty($row_RecordsetResumo['ENDERECO'])){
        
        $rst = tratar_cep($row_RecordsetResumo['ENDERECO']);
        $logradouro  = $rst['LOGRADOURO' ];
        $numero      = $rst['NUMERO'     ];
        $cep         = $rst['CEP'        ];
        $bairro      = $rst['BAIRRO'     ];
        $complemento = $rst['COMPLEMENTO'];

    }
    
    $XML = array();
    $XML['TELEFONE'     ] = $row_RecordsetResumo['TELEFONE'     ];                                          # Opcional
    $XML['NOME'         ] = $row_RecordsetResumo['NOME'         ];                                          # Obrigatorio
    $XML['CPF'          ] = soNumero($row_RecordsetResumo['CPF' ]);                                         # Opcional
    $XML['VALORPRODUTOS'] = $row_RecordsetResumo['VALORPRODUTOS'];                                          # Obrigatorio
    $XML['TROCO'        ] = !empty($row_RecordsetResumo['TROCO']) ? $row_RecordsetResumo['TROCO'] : "0.00"; # Opcional
    $XML['FORMA_PGTO'   ] = define_forma_pagamento($row_RecordsetResumo['FORMA_PGTO']);
    $XML['COMPLEMENTO'  ] = $complemento;                                                                   # Opcional
    $XML['LOGRADOURO'   ] = $logradouro;                                                                    # Obrigatorio
    $XML['NUMERO'       ] = $numero;                                                                        # Obrigatorio
    $XML['CEP'          ] = soNumero($cep);                                                                 # Obrigatorio
    $XML['BAIRRO'       ] = $bairro;                                                                        # Obrigatorio 
    $XML['CIDADE'       ] = $cidade;                                                                        # Obrigatorio 
    $XML['ESTADO'       ] = $estado;                                                                        # Obrigatorio
    $XML['ID'           ] = $controle;                                                                      # Opcional
       
    $body = draw_xml($XML);
    salva_retorno(function_soap($body, $controle), $status);
}

function draw_xml($data){
  
    
    $body  = NULL;
    $body .='<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">';
    $body .='<soap:Body>';
    $body .='  <InserirPedidoSemBag xmlns="http://www.go4you.com.br/webservices/">';
    $body .='    <token>c97e7f9f-fa2b-442e-a461-9f0bfa7f9f04</token>';
    $body .='    <emailCliente></emailCliente>';
    if($data['CPF'] != ""){
             $body .='    <cpfCliente>'. $data['CPF']  .'</cpfCliente>';
    }else{
        $body .= '<cpfCliente></cpfCliente>';
    }
    $body .='    <nomeCliente>'. $data['NOME'] .'</nomeCliente>';
    if($data['TELEFONE'] != ""){
             $body .= '<telefoneCliente>'. $data['TELEFONE'] .'</telefoneCliente>';
    }else{
        $body .= '<telefoneCliente></telefoneCliente>';
    }
    $body .='    <logradouro>'. $data['LOGRADOURO'] .'</logradouro>';
    $body .='    <numero>'. $data['NUMERO'] .'</numero>';
    $body .='    <cep>'. $data['CEP'] .'</cep>';
    $body .='    <bairro>'. $data['BAIRRO'] .'</bairro>';
    $body .='    <cidade>'. $data['CIDADE'] .'</cidade>';
    $body .='    <estado>'. $data['ESTADO'] .'</estado>';
        if($data['COMPLEMENTO'] != ""){
             $body .= '<complemento>'. $data['COMPLEMENTO'] .'</complemento>';
        }else{
            $body .= '<complemento></complemento>';
        }
    $body .='    <formaPagamentoId>'. $data['FORMA_PGTO'] .'</formaPagamentoId>';
    $body .='    <codigoPedido>'. $data['ID'] .'</codigoPedido>';
    $body .='    <valor>'. $data['VALORPRODUTOS'] .'</valor>';
    if($data['TROCO'] != ""){
             $body .= '<troco>'. $data['TROCO'] .'</troco>';
        }else{
            $body .= '<troco></troco>';
        }
    $body .='  </InserirPedidoSemBag>';
    $body .='</soap:Body>';
    $body .='</soap:Envelope>';
    
    return $body;
    
}

function SOAP_call($body, $controle){
    //define variaveis de conexao
    $location_URL = 'https://api.go4you.com.br/Consultas/Pedidos/Acoes.asmx?wsdl';
    $action_URL   =  "http://www.go4you.com.br/webservices/InserirPedidoSemBag";
    
    $client = new SoapClient(null, array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "SOAPAction: \"run\"",
            'location' => $location_URL,
            'host'     => "api.go4you.com.br",
            'uri'      => "api.go4you.com.br",
            'trace'      => "1",
        "Content-length: ".strlen($body)
    ));
    
    return $client->__doRequest($body ,$location_URL, $action_URL, 1);    
    
}

/**
* Persistencia
*/
function function_soap($body, $controle){
    
    $max   = 10;
    $count = 0;

    while($count < $max)
    {
        $order_return = SOAP_call($body, $controle);
        $doc = new DOMDocument();
        $doc->loadXML($order_return);
        
        # Verifica se retornou ID
        if(is_numeric($doc->textContent)){
            break;
        }
        
        # Verifica se retornou um erro Default
        if(is_numeric(strpos($doc->textContent, "ERRO"))){
            break;
        }
        
      sleep(5);
      $count++;
    }
    
    $status  = is_numeric($doc->textContent) ? 'true' : 'false';
    $_result = is_numeric($doc->textContent) ? $doc->textContent : utf8_decode(removeLn($doc->textContent));
    
    return $controle."||".$status."||".$_result."||".$body;
}


/**
 * Registra o resultado do envio
 */
function salva_retorno($retorno, $status_ped){
   
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $data = explode("||", $retorno);
    
    $CAIXACUPOM_CONTROLE = $data[0];
    $STATUS              = $data[1] == 'true' ? 1 : 0;
    $RESPOSTA            = $data[2];
    $XML                 = $data[3];
    
    
    # Update o status
    if($STATUS == 1){
        update_status($CAIXACUPOM_CONTROLE, $status_ped);
    }else{
        update_status($CAIXACUPOM_CONTROLE, 5, true);
    }
    
    # Limpa a tabela dos status 0 quando for TRUE
    if($STATUS == 1){
        $c_query = "DELETE FROM `GOFORYOU_PEDIDOS` WHERE `CAIXACUPOM_CONTROLE` = '{$CAIXACUPOM_CONTROLE}' ";
        $res     = mysql_query($c_query,$con);
    }
    
    # Slava o retorno
    $c_query = "INSERT INTO GOFORYOU_PEDIDOS SET                  ";
    $c_query .= " CAIXACUPOM_CONTROLE = {$CAIXACUPOM_CONTROLE},   ";
    $c_query .= " STATUS              = '{$STATUS}',              ";
    $c_query .= " RESPOSTA            = '{$RESPOSTA}',            ";
    $c_query .= " XML                 = '{$XML}',                 ";
    $c_query .= " DT_SINCRONIZACAO    = '".date("Y-m-d H:i:s")."' ";
    $res = mysql_query($c_query,$con);
    
}

function update_status($controle, $status = 0, $erro = false){
    
    # Faz pular um status direto para solicitado
    $status = $status == '1' && !($erro) ? 2 : $status;
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $c_query = "UPDATE CAIXACUPOM SET ";
    $c_query .= " STATUS_GO4YOU = '$status'               ";
    $c_query .= " WHERE CAIXACUPOM_CONTROLE = '$controle' ";
    $res = mysql_query($c_query,$con);
}

/**
 * Busca erros não visualizados
 * @param type $status
 * @return string
 */
function buscar_erro($status = 0){
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $c_query = "SELECT * FROM GOFORYOU_PEDIDOS WHERE STATUS = {$status} ";
    $RecordsetResumo = mysql_query($c_query, $con) or die(mysql_error());
    $totalRows_RecordsetResumo = mysql_num_rows($RecordsetResumo);
    
    $str = NULL;
    
    if($totalRows_RecordsetResumo > 0){
        
        do{
            if($row_RecordsetResumo['CAIXACUPOM_CONTROLE'] != ""){
                $str .= $row_RecordsetResumo['CAIXACUPOM_CONTROLE']." ".$row_RecordsetResumo['RESPOSTA']."<br>";
            }
        }while($row_RecordsetResumo = mysql_fetch_assoc($RecordsetResumo));
        
    }
    
    
    return $str;
    
}


/**
 * Lista os pedidos DELIVERY
 * @return type
 */
function consultar_opedidos(){
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $query_RecordsetResumo = "SELECT * FROM CAIXACUPOM ";
    $query_RecordsetResumo .= " WHERE TIPOATENDIMENTO = 'DELIVERY' ";
    #$query_RecordsetResumo .= " AND STATUS_GO4YOU != '4' ";
    $query_RecordsetResumo .= " ORDER BY CAIXACUPOM_CONTROLE DESC";
    $query_RecordsetResumo .= " LIMIT 20";
    $RecordsetResumo = mysql_query($query_RecordsetResumo, $con) or die(mysql_error());
    $row_RecordsetResumo = mysql_fetch_assoc($RecordsetResumo);
    $totalRows_RecordsetResumo = mysql_num_rows($RecordsetResumo);
    
    $tempo   = $_SESSION['TEMPO_PREPARACAO'];
    $pedidos = array();
    $i       = 0;
    if($totalRows_RecordsetResumo != 0 ){
        do {
            
            # Checa se percisa alterar o status
            if(dif_hora(acrescenta_min($row_RecordsetResumo['DATAHORA'  ], $tempo)) == 2 && $row_RecordsetResumo['STATUS_GO4YOU'] == "0"){
                #update_status($row_RecordsetResumo['CAIXACUPOM_CONTROLE'], 2);
                sendGo4You($row_RecordsetResumo['CAIXACUPOM_CONTROLE'], 2);
            }
            
            # Condicao para gerar contador de Tempo
            $status = " excedido ";
            if($row_RecordsetResumo['STATUS_GO4YOU'] == "0" && dif_hora(acrescenta_min($row_RecordsetResumo['DATAHORA'  ], $tempo)) != 2){
                $status = dif_hora_draw(acrescenta_min($row_RecordsetResumo['DATAHORA'], $tempo), date('Y-m-d H:i:s'));
            }
            
            $pedidos[$i]['CAIXACUPOM_CONTROLE'] = $row_RecordsetResumo['CAIXACUPOM_CONTROLE'     ];
            $pedidos[$i]['DATAHORA'           ] = dataMySql2BR($row_RecordsetResumo['DATAHORA'   ]);
            $pedidos[$i]['TEMPO_PREVISTO'     ] = acrescenta_min($row_RecordsetResumo['DATAHORA' ], $tempo);
            $pedidos[$i]['STATUS_GO4YOU'      ] = trata_texto(status($row_RecordsetResumo['STATUS_GO4YOU']));
            $pedidos[$i]['STATUS_GO4YOU_COD'  ] = $row_RecordsetResumo['STATUS_GO4YOU'];
            $pedidos[$i]['TEMPO'              ] = $status;
            
            if($row_RecordsetResumo['STATUS_GO4YOU'] < 4){
                $pedidos[$i]['STATUS_BTN'     ] = '<input type="submit" id="btn-'.$pedidos[$i]['CAIXACUPOM_CONTROLE'].'" value="'.trata_texto(status($row_RecordsetResumo['STATUS_GO4YOU'] + 1)).'" onClick="update_status('.$row_RecordsetResumo['CAIXACUPOM_CONTROLE'].', '.$row_RecordsetResumo['STATUS_GO4YOU'].');button_message(this.id)" style="cursor: pointer;" />';
            }elseif($row_RecordsetResumo['STATUS_GO4YOU'] == 5){
                $pedidos[$i]['STATUS_BTN'     ] = '<input type="button" id="btn-'.$pedidos[$i]['CAIXACUPOM_CONTROLE'].'" value="'.trata_texto(status(1)).'" onClick="update_status('.$row_RecordsetResumo['CAIXACUPOM_CONTROLE'].', 1);button_message(this.id)" style="cursor: pointer;" />';
            }else{
                $pedidos[$i]['STATUS_BTN'     ] = '<input type="button" id="btn-'.$pedidos[$i]['CAIXACUPOM_CONTROLE'].'" value="Entregue" disabled />';
            }
            ++$i;
        } while ($row_RecordsetResumo = mysql_fetch_assoc($RecordsetResumo)); 
    }
    
    return json_encode($pedidos);
    
}

function button_update($controle, $status){
    sendGo4You($controle, $status);
}

$sajax_request_type = "GET"; //forma como os dados serao enviados
sajax_init(); //inicia o SAJAX
sajax_export("button_update","buscar_erro","data_hora","update_status", "consultar_opedidos" ,"apagarBagPedido" ,"apagarBag", "lerBag","excluir_cadastro"); // lista de funcoes a ser exportadas
sajax_handle_client_request();// serve instancias de clientes

$_SESSION['titulo'] = 'Go4You LerFormasPagamento';
include('../admin/header.php');

?>
<script language="javascript" src="go4you.js?v=035"></script>
<script language="javascript"><!--
<?
sajax_show_javascript(); //gera o javascript
?>
//-->
</script>




<form action="" method="post" name="form2" id="form2">
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="400"></td>
      <td width="400"></td>
      <td width="400" align="right"><span class="titulo_pagina">Go4You Pedidos</span></td>
    </tr>
    <tr>
      <td height="15"></td>
      <td></td>
    </tr>
  </table>
  
<table width="798" border="0" align="center" cellspacing="5" class="grade_dados">
  <tr>
    <td width="8%" align="right">MINUTOS:</td>
    <td width="5%" align="left"><input name="TEMPO_PREPARACAO" type="number" id="TEMPO_PREPARACAO" size="4" value="<?php echo $_SESSION['TEMPO_PREPARACAO']; ?>" required /></td>
    <td width="20%" align="right" colspan="3" id="just-time"><?php echo dataMySql2BR(date('Y-m-d H:i:s')); ?></td>
    <td width="10%"></td>
    <td width="14%" align="center"><input type="submit" value="Aplicar Tempo" /></td>
  </tr>
</table>
</form>

<br />
<table width="800" border="0" cellspacing="1" cellpadding="3" align="center" id="table-lista-erros" style="display:none;">
    <tbody id="lista-erros">
        <tr id="" onMouseOut="" onMouseOver=""  bgcolor="#F2F2F2">
            <td></td>
        </tr>
    </tbody>
</table>

<table width="800" border="0" cellspacing="1" cellpadding="3" align="center">
    <thead>
        <tr bgcolor="#0066FF">
          <td width="50" height="20" bgcolor="#0066FF" class="style6"><div align="left">C&oacute;digo</div></td>
          <td width="150" class="style6"><div align="left">Hora Pedido</div></td>
          <td width="50" class="style6"><div align="left">Tempo</div></td>
          <td width="80" class="style6"><div align="left">Status Atual</div></td>
          <td width="50" class="style6">Próximo Status</td>
        </tr>
    </thead>
    <tbody id="lista-peidos">
        <tr id="" onMouseOut="" onMouseOver=""  bgcolor="#F2F2F2">
            <td colspan="5">Carregando...</td>
        </tr>
    </tbody>
  </table>

<p>&nbsp;</p>

</body>
</html>
<?php
mysql_free_result($RecordsetResumo);
?>