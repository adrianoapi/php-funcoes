<?php


session_start();
require_once('../Connections/hhsystem.php');
include('../admin/restrito.php');
require_once('../../hhsystem/funcoes/funcoes.php');
require_once('../admin/geral.php');
include('../admin/config.php');
require_once('../../hhsystem/funcoes/Sajax.php');

//======================================================================
// ALGORITIMO PARA EXIBICAO DE PEDIDOS DA HUB2B
//======================================================================


//-----------------------------------------------------
// Funcoes de auxilio de tratamento das variaveis
//-----------------------------------------------------
$iPagina = !isset($_GET['pagina']) ? 1  : $_GET['pagina']; 
$nome    = !isset($_GET['nome'  ]) ? '' : $_GET['nome'  ];
$codigo  = !isset($_GET['codigo']) ? '' : $_GET['codigo']; 
$inicio  = ($iPagina - 1) * 50;


if (isset($_GET['ordem'])) {
  $campo_ordem = $_GET['ordem'];
  $by = ($campo_ordem != 'PRODUTO_CONTROLE' ? 'ASC' : 'DESC');
} else {
  $by = 'DESC';
  $campo_ordem = 'PRODUTO_CONTROLE';
}

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

/**
 * Descreve o status do pedido
 * @param type $status
 * @return string
 */
function label_status($status){
    
    switch ($status) {
        case 'payment-pending':
            return 'Pagamento Pendente';
            break;
        case 'payment-approved':
            return 'Pagamento Aprovado';
            break;
        case 'order-accepted':
            return 'Verificando Envio';
            break;
        case 'invoiced':
            return 'Faturado';
            break;
        case 'shipped':
            return 'Enviado';
            break;
        case 'delivered':
            return 'Entregue';
            break;
        case 'canceled':
            return 'Cancelado';
            break;
        default:
            return 'Pagamento Pendente';
            break;
    }
    
}

function define_status($status){
    
    switch ($status) {
        case 1:
            return 'payment-pending';
            break;
        case 2:
            return 'payment-approved';
            break;
        case 3:
            return 'order-accepted';
            break;
        case 4:
            return 'invoiced';
            break;
        case 5:
            return 'shipped';
            break;
        case 6:
            return 'delivered';
            break;
        case 0:
            return 'canceled';
            break;
        default:
            return 'payment-pending';
            break;
    }
    
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

function gera_json($CFOP, $VALORNF, $STATUS, $NUMERONF, $CHAVENFE, $RASTREIOPEDIDO, $DATAHORA, $ITEM){
    return $str = '{
    "type": "Output",
    "status": "'. define_status($STATUS) .'",
    "invoiceNumber": "'.$NUMERONF.'",
    "trackingNumber": "'.$RASTREIOPEDIDO.'",
    "items" : '. $ITEM .',
    "issuanceDate": "'. str_replace(" ", "T", $DATAHORA) .'",
    "invoiceValue": '.($VALORNF * 100).',
    "invoiceKey": "'.$CHAVENFE.'",
    "invoiceSeries": "1",
    "cfop": "'.$CFOP.'"
  }';
}

function gera_json_item( $DATA ){

    $count  = 0;
    $string = NULL;
    foreach($DATA as $VALUE):
        $string .= $count > 0 ? "," : NULL;
        $string .= '{
                    "id": "'.$VALUE['PRODUTO_CONTROLE'].'",
                    "quantity": '. round($VALUE['QUANTIDADE']) .',
                    "price": '.$VALUE['VALORTOTAL'].'
                  }';
        $count++;
    endforeach;
    
    return $count > 1 ? "[{$string}]" : $string;
    
}

//-----------------------------------------------------
// Funcoes para interacao com banco de dados
//-----------------------------------------------------  


/**
 * Faz select nas notas fiscais geradas a partir dos pedidos da hub2b
 * @param type $NF
 * @return type
 */
function get_nf($NF){
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $s_query  = "SELECT HB.HUB2B_PEDIDO_CONTROLE,"
            . " NF.RASTREIOPEDIDO, "
            . " NF.STATUS,         "
            . " NF.VALORNF,        "
            . " NF.NUMERONF,       "
            . " NF.CHAVENFE,       "
            . " NF.EMISSAO,        "
            . " CF.CODIGO          ";
    $s_query .= " FROM HUB2B_PEDIDO AS  HB                                     ";
    $s_query .= " INNER JOIN NF         ON NF.NF_CONTROLE   = HB.NF_CONTROLE   ";
    $s_query .= " INNER JOIN CFOP AS CF ON CF.CFOP_CONTROLE = NF.CFOP_CONTROLE ";
    $s_query .= " WHERE NF.NF_CONTROLE = {$NF} ORDER BY HB.NF_CONTROLE DESC    ";
    $s_query .= " LIMIT 1                                                      ";
    $c_Resumo = mysql_query($s_query, $con) or die(mysql_error());
    $rst      = mysql_fetch_assoc($c_Resumo);
    
    return $rst;
    
}

/**
 * Monta um array de itens da nota
 * @param type $NF
 * @return type
 */
function get_nf_item($NF){
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $result   = array();
    $s_query  = "SELECT PRODUTO_CONTROLE, QUANTIDADE, VALORTOTAL  FROM NFITEM ";
    $s_query .= " WHERE NF_CONTROLE = {$NF}                                   ";
    $c_Resumo = mysql_query($s_query, $con) or die(mysql_error());
    
    do{
        if($rst['PRODUTO_CONTROLE'] != ""){
            $result[] = array(
                'PRODUTO_CONTROLE' => $rst['PRODUTO_CONTROLE'],
                'QUANTIDADE'       => $rst['QUANTIDADE'      ],
                'VALORTOTAL'       => $rst['VALORTOTAL'      ]);
        }
    } while ($rst = mysql_fetch_assoc($c_Resumo));
    
    return $result;
    
}


/**
 * Faz a sincronização
 * @param type $DATA
 */
function sincronizarMassa($DATA){
    
    mysql_select_db($database_hhsystem, $hhsystem);
    
    $NOTAS  = explode(",", $DATA);
    $result = NULL;
    $c_json = 0;
    $json   = NULL;
    foreach ($NOTAS as $NF):
    
        # Faz select nas notas fiscais geradas a partir dos pedidos da hub2b
        $rst  = get_nf( $NF );
        
        # Monta a lista de itens
        $item = get_nf_item($NF);
        $item = gera_json_item($item);
        
        # Monta o JSON
        $result = gera_json(
                $rst['CODIGO'        ],
                $rst['VALORNF'       ],
                $rst['STATUS'        ],
                $rst['NUMERONF'      ],
                $rst['CHAVENFE'      ],
                $rst['RASTREIOPEDIDO'],
                $rst['EMISSAO'       ],
                $item
                );
        $json  .= $c_json > 0 ? "," : NULL;
        $json  .= trata_texto("$result");

        $c_json++;
        
    endforeach;
    
    # Sincroniza com o WS
    $ws_ret   = postApi(NULL, 'invoiceorder', array('order' => $rst['HUB2B_PEDIDO_CONTROLE']), "[{$json}]");
    
    # Trata a resposta do produto
    # Verifica se o WS HUB2B retornou sem erro
    if($ws_ret->error == ""){
        $rst = array('status' => true, 'msg' => 'Sincronizado com sucesso!');
    }else{
        $rst = array('status' => false,'msg' => 'Erro ao sincronizar!', 'error' => ($ws_ret));
    }
    print_r(json_encode($rst));
        
}


# Faz select nas notas fiscais geradas a partir dos pedidos da hub2b
mysql_select_db($database_hhsystem, $hhsystem);
$c_RecordsetResumoTotal  = "SELECT HP.*, PE.NOME                                                ";
$c_RecordsetResumoTotal .= " FROM HUB2B_PEDIDO AS HP                                            ";
$c_RecordsetResumoTotal .= " INNER JOIN NF ON NF.NF_CONTROLE = HP.NF_CONTROLE                   ";
$c_RecordsetResumoTotal .= " INNER JOIN PESSOA AS PE ON PE.PESSOA_CONTROLE = NF.PESSOA_CONTROLE ";
$c_Resumo      = mysql_query($c_RecordsetResumoTotal, $hhsystem) or die(mysql_error());
$r_ResumoTotal = mysql_num_rows($c_Resumo);


$sajax_request_type = "GET";       # forma como os dados serao enviados
sajax_init();                      # inicia o SAJAX
sajax_export("sincronizarMassa");  # lista de funcoes a ser exportadas
sajax_handle_client_request();     # serve instancias de clientes

$_SESSION['titulo'] = 'Cadastro de produto Hub2b';
include('../admin/header.php');

?>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css" rel="stylesheet" />
<style type="text/css">
    select {
        font-family: 'FontAwesome', 'sans-serif';
    }
</style>
<script language="javascript" src="hub2b_pedidos.js?v=004"></script>
<script language="javascript"><!--
<?
sajax_show_javascript(); //gera o javascript
?>
//-->
</script>


<form action="" method="get" name="form2" id="form2">
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="400"></td>
      <td width="400" align="right"><span class="titulo_pagina"><a href="hub2b_pedido_ws.php">Atualizar Pedidos</a></span></td>
      <td width="400" align="right"><span class="titulo_pagina">Atualização pedidos Hub2b</span></td>
    </tr>
    <tr>
      <td height="15"></td>
      <td></td>
      <td></td>
    </tr>
  </table>
  
<table width="798" border="0" align="center" cellspacing="5" class="grade_dados">
  <tr>
    <td width="8%" align="right">ID Produto:</td>
    <td width="5%" align="left"><input name="codigo" type="text" id="id" size="4" value="<?php echo $codigo;?>" /></td>
    <td width="7%" align="right">Nome:</td>
    <td width="16%"><input name="nome" type="text" id="nome" size="20" value="<?php echo $nome;?>" /></td>
    <td width="13%" align="right">Ordenar por:</td>
    <td width="10%"><select name="ordem" id="ordem">
      <option value="PRODUTO_CONTROLE" <?php if (!(strcmp("PRODUTO_CONTROLE", $campo_ordem))) {echo "SELECTED";} ?>>C&oacute;digo</option>
      <option value="NOME" <?php if (!(strcmp("NOME", $campo_ordem))) {echo "SELECTED";} ?>>Nome</option>
    </select></td>
    <td width="14%" align="center"><input type="submit" value="Buscar" /></td>
  </tr> 
</table>
<br />
 
<?php 
if($r_ResumoTotal != 0 ){ ?>   
  <table width="800" border="0" cellspacing="1" cellpadding="3" align="center">   
    <tr bgcolor="#0066FF">
      <!--<td width="100" height="20" bgcolor="#0066FF" class="style6"><div align="left">C&oacute;digo</div></td>-->
      <td width="100" bgcolor="#0066FF" class="style6"><div align="left">NF ID</div></td>
      <td width="140" bgcolor="#0066FF" class="style6"><div align="left">Código Pedido</div></td>
      <td width="140" bgcolor="#0066FF" class="style6"><div align="left">Status</div></td>
      <td width="460" class="style6"><div align="left">Nome</div></td>
      <td width="1%" align="center" class="titulo_grade"><input type="checkbox" onClick="check_all(this)" /></td>
    </tr>
    <?php 
      do { 
          if($row_Resumo['HUB2B_PEDIDO_CONTROLE'] != ""){
        ?>
        <tr id="<?php echo 'linha_2'.$ii?>" onMouseOut="cor_linha1('2','<?php echo $ii?>')" onMouseOver="cor_linha1('1','<?php echo $ii?>')"  bgcolor="#F2F2F2">
          <td><?php echo $row_Resumo['NF_CONTROLE']; ?></td>
          <td><?php echo $row_Resumo['HUB2B_PEDIDO_CONTROLE']; ?></td>
          <td><?php echo label_status($row_Resumo['STATUS']); ?></td>
          <td><?php echo $row_Resumo['NOME']; ?></td>
          <td align="center">
            <label id="checkSincronizar">
                <input type="checkbox" class="flat" name="exportar[]" value="<?php echo $row_Resumo['NF_CONTROLE']; ?>">
            </label>
          </td>
        </tr>
    <?php 
      $ii++;
          }
      } while ($row_Resumo = mysql_fetch_assoc($c_Resumo)); 
      ?>
        <tr bgcolor="#F2F2F2">
            <td align="right" colspan="5">
                <input name="" id="btn-sincronizar" type="button" value="Sincronizar" onclick="sincronizarMassa()">
            </td>
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
    $TotalPages = ceil($r_ResumoTotal/$total_reg);
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