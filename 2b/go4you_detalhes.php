<?php
session_start();
require_once('../Connections/hhsystem.php');
require_once('../../hhsystem/funcoes/funcoes.php');
require_once('../../hhsystem/funcoes/Sajax.php');
include('../admin/geral.php');
include('../admin/config.php');

$sajax_request_type = "GET"; //forma como os dados serao enviados
sajax_init(); //inicia o SAJAX
sajax_export("salvar_cadastro","busca_produto"); // lista de funcoes a ser exportadas
sajax_handle_client_request();// serve instancias de clientes
################## GET ##################


if (isset($_GET['bag'])) {
	$bag = true;
} else {
	$bag = false;
}



$body = '
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <LerFormasPagamento xmlns="http://www.go4you.com.br/webservices/">
      <token>c97e7f9f-fa2b-442e-a461-9f0bfa7f9f04</token>
    </LerFormasPagamento>
  </soap:Body>
</soap:Envelope>';
$client = new SoapClient("https://api.go4you.com.br/Consultas/Pedidos/Acoes.asmx?wsdl", array('trace' => 1));
$params = array(
  "token" => "c97e7f9f-fa2b-442e-a461-9f0bfa7f9f04",
);


$response = $client->__soapCall("LerFormasPagamento", array($params));
$response_fp = json_decode($response->LerFormasPagamentoResult);



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



function salvar_cadastro($BAG, $EMAIL, $CPF, $NOME, $TELEFONE, $LOGRADOURO, $NUMERO, $CEP, $BAIRRO, $CIDADE, $ESTADO, $COMPLEMENTO, $FORMAPA_GAMENTO, $CODIGO_PEDIDO, $VALOR, $TROCO) {
    
    if($BAG > 0){
        # Com BAG
        $body = '<?xml version="1.0" encoding="utf-8"?>
                 <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                  <soap:Body>
                    <InserirPedidoComBag xmlns="http://www.go4you.com.br/webservices/">
                    <token>c97e7f9f-fa2b-442e-a461-9f0bfa7f9f04</token>
                    <bagId>'. $BAG .'</bagId>
                    <emailCliente>'. $EMAIL .'</emailCliente>
                    <cpfCliente>'. $CPF .'</cpfCliente>
                    <nomeCliente>'. $NOME .'</nomeCliente>
                    <telefoneCliente>'. $TELEFONE .'</telefoneCliente>
                    <logradouro>'. $LOGRADOURO .'</logradouro>
                    <numero>'. $NUMERO .'</numero>
                    <cep>'. $CEP .'</cep>
                    <bairro>'. $BAIRRO .'</bairro>
                    <cidade>'. $CIDADE .'</cidade>
                    <estado>'. $ESTADO .'</estado>
                    <complemento>'. $COMPLEMENTO .'</complemento>
                    <formaPagamentoId>'. $FORMAPA_GAMENTO .'</formaPagamentoId>
                    <codigoPedido>'. $CODIGO_PEDIDO .'</codigoPedido>
                    <valor>'. $VALOR .'</valor>
                    <troco>'. $TROCO .'</troco>
                  </InserirPedidoComBag>
                 </soap:Body>
              </soap:Envelope>';
    }else{
        # Sem BAG
        $body = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                  <InserirPedidoSemBag xmlns="http://www.go4you.com.br/webservices/">
                    <token>c97e7f9f-fa2b-442e-a461-9f0bfa7f9f04</token>
                    <emailCliente>'. $EMAIL .'</emailCliente>
                    <cpfCliente>'. $CPF .'</cpfCliente>
                    <nomeCliente>'. $NOME .'</nomeCliente>
                    <telefoneCliente>'. $TELEFONE .'</telefoneCliente>
                    <logradouro>'. $LOGRADOURO .'</logradouro>
                    <numero>'. $NUMERO .'</numero>
                    <cep>'. $CEP .'</cep>
                    <bairro>'. $BAIRRO .'</bairro>
                    <cidade>'. $CIDADE .'</cidade>
                    <estado>'. $ESTADO .'</estado>
                    <complemento>'. $COMPLEMENTO .'</complemento>
                    <formaPagamentoId>'. $FORMAPA_GAMENTO .'</formaPagamentoId>
                    <codigoPedido>'. $CODIGO_PEDIDO .'</codigoPedido>
                    <valor>'. $VALOR .'</valor>
                    <troco>'. $TROCO .'</troco>
                  </InserirPedidoSemBag>
                </soap:Body>
              </soap:Envelope>';
    }
    //Change this variables.
    $location_URL = 'https://api.go4you.com.br/Consultas/Pedidos/Acoes.asmx?wsdl';
    $action_URL =  $BAG > 0 ? "http://www.go4you.com.br/webservices/InserirPedidoComBag" : "http://www.go4you.com.br/webservices/InserirPedidoSemBag";

    $client = new SoapClient(null, array(
        'location' => $location_URL,
        'uri'      => "",
        'trace'    => 1,
    ));

    $order_return = $client->__doRequest($body ,$location_URL ,$action_URL , 1);
    $doc = new DOMDocument();
    $doc->loadXML($order_return);

    return is_numeric($doc->textContent) ? "true||".$doc->textContent :  "false||".utf8_encode($doc->textContent);

}
  
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<base target="_self">
<title>Cadastro Buscape</title>
<script type="text/javascript" src="../../hhsystem/funcoes/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../../hhsystem/funcoes/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../style/jquery.fancybox.css?v=2.1.6" media="screen">
<script language="javascript" src="../../hhsystem/funcoes/funcoes.js"></script>
<script language="javascript" src="go4you_detalhes.js?v=027"></script>
<script language="javascript">   	
  <?
    sajax_show_javascript( ); //gera o javascript
  ?>  
</script>


<link rel="stylesheet" type="text/css" media="screen" href="../complete/resources/css/smoothness/jquery-ui-1.10.1.custom.css"/>
<link rel="stylesheet" type="text/css" media="screen" href="../complete/resources/css/smoothness/jquery.ui.combogrid.css"/>
<link rel="stylesheet" href="../style/style.css" TYPE="text/css">

<!--<script type="text/javascript" src="../complete/resources/jquery/jquery-1.9.1.min.js"></script>-->
<script type="text/javascript" src="../complete/resources/jquery/jquery-ui-1.10.1.custom.min.js"></script>
<script type="text/javascript" src="../complete/resources/plugin/jquery.ui.combogrid-1.6.3.js?006"></script>



<body>   
  <form action="" method="post" name="form_detalhe" id="form_detalhe">
      <input name="HUB2B_PRODUTO_CONTROLE" type="hidden" id="HUB2B_PRODUTO_CONTROLE" value="<?php echo !is_null($HUB2B_PRODUTO_CONTROLE) ? $HUB2B_PRODUTO_CONTROLE : ''; ?>">
      <P>&nbsp;</P>  
      <table width="1180px" align="center" valign="top" style="position:relative; top:-10px">
          <tr valign="top">
            <td style="position:absolute; width:85%">
            <?php if(!$visualizar && !$sincronizar && $HUB2B_PRODUTO_CONTROLE == 0){ ?>
              <!--TELA CADASTRO-->                                      
              <table width="85%" align="left" class="grade_dados_nota">
                <tr>
                  <td width="20"></td>
                  <td colspan="3"><strong>CADASTRO DE PEDIDO</strong></td>
                </tr>
                <tr>
                  <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                  <td width="20"></td>
                  <td width="100"><strong>EMAIL</strong></td>
                  <td width="100"><strong>CPF</strong></td>
                  <td width="100"><strong>NOME</strong></td>
                </tr>
                <tr>
                  <td></td>
                  <td><input name="EMAIL" value="suporte10@cwd.com.br" style="width:100%" type="text" id="EMAIL" size="14" maxlength="14" tabindex="26"></td>
                  <td><input name="CPF"   value="3572289800"           style="width:100%" type="text" id="CPF"  tabindex="27"/></td>
                  <td><input name="NOME"  value="José da Silva"        style="width:100%" type="text" id="NOME" class="campo" style="width:100%"></td>
                </tr>
                <tr>
                  <td></td>
                  <td><strong>TELEFONE</strong></td>
                  <td><strong>LOGRADOURO</strong></td>
                  <td><strong>NÚMERO</strong></td>
                </tr>
                <tr>
                  <td></td>
                  <td><input name="TELEFONE"   value="11945043507"   style="width:100%" type="text" id="TELEFONE" size="14" maxlength="14" tabindex="26"></td>
                  <td><input name="LOGRADOURO" value="Rua Marambaia" style="width:100%" type="text" id="LOGRADOURO"  tabindex="27"/></td>
                  <td><input name="NUMERO"     value="158"           style="width:50%"  type="text" id="NUMERO" class="campo"  value="" style="width:100%"></td>
                </tr>
                <tr>
                  <td></td>
                  <td><strong>CEP</strong></td>
                  <td><strong>BAIRRO</strong></td>
                  <td><strong>CIDADE</strong></td>
                </tr>
                <tr>
                  <td></td>
                  <td><input name="CEP"    value="02513000"   style="width:50%"  type="text" id="CEP" size="14" maxlength="14" tabindex="26"></td>
                  <td><input name="BAIRRO" value="Casa Verde" style="width:100%" type="text" id="BAIRRO"  tabindex="27"/></td>
                  <td><input name="CIDADE" value="São Paulo"  style="width:100%" type="text" id="CIDADE" class="campo" value="" style="width:100%"></td>
                </tr>
                <tr>
                  <td></td>
                  <td><strong>ESTADO</strong></td>
                  <td><strong>COMPLEMENTO</strong></td>
                  <td><strong>FORMA DE PAGAMENTO</strong></td>
                </tr>
                <tr>
                  <td></td>
                  <td><input name="ESTADO"      value="SP" style="width:100%" type="text" id="ESTADO" size="14" maxlength="2" tabindex="26"></td>
                  <td><input name="COMPLEMENTO" value="casa"      style="width:100%" type="text" id="COMPLEMENTO"   tabindex="27"/></td>
                  <td>
                      <select name="FORMAPA_GAMENTO" id="FORMAPA_GAMENTO" style="width:100%">
                      <?php
                        foreach($response_fp as $value):
                            echo "<option value=\"". $value->Id ."\">". utf8_decode($value->Nome) ."</option>";
                        endforeach;
                      ?>
                      </select>
                  </td>
                </tr>
                <tr>
                  <td></td>
                  <td><strong>COD PEDIDO</strong></td>
                  <td><strong>VALOR</strong></td>
                  <td><strong>TROCO</strong></td>
                </tr>
                <tr>
                  <td></td>
                  <td><input name="CODIGO_PEDIDO" value="<?php echo rand(1, 500); ?>"      style="width:50%" type="text" id="CODIGO_PEDIDO" size="14" maxlength="14" tabindex="26"></td>
                  <td><input name="VALOR"         value="50.99" style="width:50%" type="text" id="VALOR"  tabindex="27"/></td>
                  <td><input name="TROCO"         value="0.00"  style="width:50%" type="text" id="TROCO" class="campo" value="" style="width:100%"></td>
                </tr>
                <?php if($bag) { ?>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><strong>BAG</strong></td>
                </tr>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td><input name="BAG" value=""  style="width:50%" type="text" id="BAG" class="campo" value="" style="width:100%"></td>
                </tr>
                <?php }else{ ?>
                <input type="text" name="BAG" id="BAG" value="0">
                <?php } ?>
                <tr>
                  <td colspan="4"></td>
                </tr>
                <tr>
                  <td width="20"></td>
                  <td colspan="3">
                    <input type="button" name="button" value="Salvar dados" onClick="if (!!menu_novo('<?php echo $_SESSION['usr_pode_banco'];?>','I','A',document.getElementById('HUB2B_PRODUTO_CONTROLE').value)){ salvar_cadastro(); }" style="cursor:pointer;" >
                  </td>
                </tr>                              
              </table>
                <!--/TELA CADASTRO-->
                <?php } ?>
                
                <?php if(!$visualizar && !$sincronizar && $HUB2B_PRODUTO_CONTROLE != 0){ ?>
                <!--TELA ALTERAR CADASTRO-->  
                <table width="85%" align="left" class="grade_dados_nota">
                  <tr>
                    <td width="20"></td>
                    <td colspan="3"><strong>ALTERAR CADASTRO HUB2B</strong></td>
                  </tr>
                  <tr>
                    <td colspan="4">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="20"></td>
                    <td width="100"><strong>Código</strong></td>
                    <td width="50"><strong>Descrição</strong></td>
                    <td width="300"><strong>Link</strong></td>
                  </tr>
                  <tr>
                    <td width="20"></td>
                    <td><input name="CODIGO_4" style="width:90px" value="<?php echo $CODIGO; ?>" type="text" id="CODIGO_4" size="14" maxlength="14" disabled="true"/></td>
                    <td><input name="DESCRICAO_4" value="<?php echo $NOME; ?>" style="width:100%" type="text" id="DESCRICAO_4" size="40" maxlength="40"  disabled="true"/></td>
                    <td><input name="LINK" type="text" class="campo" id="LINK" value="<?php echo $LINK; ?>" style="width:100%"></td>
                  </tr>
                  <tr>
                      <td colspan="4">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="20"></td>
                    <td width="100">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="146" colspan="4">
                        <p><strong>Categoria:</strong> <?php echo $GRUPO_NOME_FORM; ?></p>
                    </td>
                  </tr>
                  <tr>
                    <td width="20"></td>
                    <td colspan="3">
                      <input name="HUB2B_PRODUTO_CONTROLE" id="HUB2B_PRODUTO_CONTROLE" type="hidden" value="<?php echo $HUB2B_PRODUTO_CONTROLE; ?>">
                      <input type="button" name="button" value="Salvar dados" onClick="if (!!menu_novo('<?php echo $_SESSION['usr_pode_banco'];?>','I','A',document.getElementById('HUB2B_PRODUTO_CONTROLE').value)){ salvar_cadastro(); }" style="cursor:pointer;" >
                    </td>
                  </tr>
                </table>
                <!--/TELA ALTERAR CADASTRO-->
                <?php }?>
            </td>
            
            <input type="hidden" name="LINHA_" id="LINHA_" value="1" />
            <input type="hidden" name="CODIGOBARRA" id="CODIGOBARRA" value="" />
    

          </tr>          
        </table>
     </form>
   
    
  </body>
</html>