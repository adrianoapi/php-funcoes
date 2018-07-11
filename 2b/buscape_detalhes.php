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
 if (isset($_GET['sincronizar'])) {
	$sincronizar = true;
} else {
	$sincronizar = false;
}

if (isset($_GET['visualizar'])) {
	$visualizar = true;
} else {
	$visualizar = false;
}

if (isset($_GET['cmd'])) {
	$id_controle = $_GET['cmd'];
} else {
	$id_controle = '';
}

if (($_GET['pagina'])) {
	$iPagina = $_GET['pagina'];
} else {
	$iPagina = '1';
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

$cfg_bp               = array();
$cfg_bp['app-token']  = '41fa5164-493c-3949-b828-8563feafc367';

mysql_select_db($database_hhsystem, $hhsystem);
$query_RecordsetProduto = "SELECT BP.*,"
                                  . " P.NOME,"
                                  . " PLE.SALDO AS QUANTIDADE,"
                                  . " P.DESCRICAO_LOJA,"
                                  . " P.ALTURA,"
                                  . " P.LARGURA,"
                                  . " P.COMPRIMENTO,"
                                  . " P.PESO_LIQUIDO,"
                                  . " PC.CODIGO AS CODIGO_BARRA,"
                                  . " MA.NOME AS NOME_MARCA,"
                                  . " PG.GRUPO_CONTROLE,"
                                  . " GR.NOME AS GRUPO_NOME";
$query_RecordsetProduto .= " FROM BUSCAPE_PRODUTO AS BP ";
$query_RecordsetProduto .= " LEFT JOIN PRODUTO             P   ON BP.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
$query_RecordsetProduto .= " LEFT JOIN PRODUTOCODIGO       PC  ON PC.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
$query_RecordsetProduto .= " LEFT JOIN PRODUTOLOCALESTOQUE PLE ON PC.PRODUTO_CONTROLE = PLE.PRODUTO_CONTROLE AND PC.PRODUTOVARIANTE_CONTROLE = PLE.PRODUTOVARIANTE_CONTROLE ";
$query_RecordsetProduto .= " LEFT JOIN MARCA               MA  ON MA.MARCA_CONTROLE   = P.MARCA_CONTROLE ";
$query_RecordsetProduto .= " LEFT JOIN PRODUTOGRUPO        PG  ON PG.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
$query_RecordsetProduto .= " LEFT JOIN GRUPO               GR  ON GR.GRUPO_CONTROLE   = PG.GRUPO_CONTROLE ";
$query_RecordsetProduto .= " WHERE BP.BUSCAPE_PRODUTO_CONTROLE =  '$id_controle' ";
$RecordsetProduto = mysql_query($query_RecordsetProduto, $hhsystem) or die(mysql_error());
$row_RecordsetProduto = mysql_fetch_assoc($RecordsetProduto);
$totalRows_RecordsetProduto = mysql_num_rows($RecordsetProduto);

if ($totalRows_RecordsetProduto != 0 ) {
    $BUSCAPE_PRODUTO_CONTROLE    =   $row_RecordsetProduto['BUSCAPE_PRODUTO_CONTROLE'];
    $PRODUTO_CONTROLE            =   $row_RecordsetProduto['PRODUTO_CONTROLE'];
    $CODIGO_BARRA                =   $row_RecordsetProduto['CODIGO_BARRA'];
    $CODIGO                      =   $row_RecordsetProduto['CODIGO'];
    $DESCRICAO_LOJA              =   $row_RecordsetProduto['DESCRICAO_LOJA'];
    $NOME                        =   $row_RecordsetProduto['NOME'];
    $GRUPO_NOME                  =   $row_RecordsetProduto['GRUPO_NOME'];
    $GRUPO_NOME_FORM             =   $row_RecordsetProduto['GRUPO_NOME'];
    $ALTURA                      =   round($row_RecordsetProduto['ALTURA'] * 100);
    $LARGURA                     =   round($row_RecordsetProduto['LARGURA'] * 100);
    $COMPRIMENTO                 =   round($row_RecordsetProduto['COMPRIMENTO'] * 100);
    $PESO_LIQUIDO                =   $row_RecordsetProduto['PESO_LIQUIDO'] * 1000;
    $LINK                        =   $row_RecordsetProduto['LINK'];
    $MARCA                       =   $row_RecordsetProduto['NOME_MARCA'];
    $GRUPO_CONTROLE              =   $row_RecordsetProduto['GRUPO_CONTROLE'];
    $QUANTIDADE                  =   $row_RecordsetProduto['QUANTIDADE'];
   
    $con = mysql_connect( $GLOBALS[ 'hostname_hhsystem' ], $GLOBALS[ 'username_hhsystem' ], $GLOBALS[ 'password_hhsystem' ] );
    mysql_select_db( $GLOBALS[ 'database_hhsystem' ] );
    
    // Define o caminho da imagem
    $caminho = $_SESSION['CAMINHO_FOTO'];
    $caminho.  'userfiles/' . $PRODUTO_CONTROLE.  '/sistema/mini.jpg';
    
    // IMAGENS
    $query_imagem = "SELECT NOME_IMAGEM FROM PRODUTOIMAGEM WHERE PRODUTO_CONTROLE = '$PRODUTO_CONTROLE'";
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
    
    if($sincronizar != true){
        // PEGA AS CATEGORIAS DO BUSCAPE DE ACORDO COM O GRUPO DO PRODUTO
        # BEGIN CATEGORIA
        $categoria_pesquisa = str_replace(" ", "+", trim( strtr($GRUPO_NOME, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC")));
        $xml_cat = file_get_contents('http://sandbox.buscape.com/service/findCategoryList/412f4b4a4268414258674d3d/BR/?keyword='.$categoria_pesquisa, false, null);
        $xml = simplexml_load_string($xml_cat);
        if ($xml === false) {
        } else {
            $array_cateogiras = array();
            foreach($xml->subCategory as $value):
                $array_cateogiras[] = utf8_decode($value->name);
            endforeach;
            $xml = true;
        }
        # END CATEGORIA
    }
      
    $str_itens = NULL;
    if($totalRows_RecordsetComp != 0 ){
        do {
        $str_itens .= " -". $rowComposicao['NOME'];
        } while ($rowComposicao = mysql_fetch_assoc($RecordsetComp)); 
    }
    
     // Validar campos obrigatórios
    $msg_erro = array();
    if(empty($NOME)){
        $msg_erro[] = "Nome inválido: ".$NOME;
    }
    if(!is_numeric($PRODUTO_CONTROLE)){
        $msg_erro[] = "ID inválido: ".$PRODUTO_CONTROLE;
    }
    if(empty($row_RecordsetProduto['BUSCAPE_CATEGORIA'])){
        $msg_erro[] = "Categoria inválida: informe uma categoria";
    }
    if($ALTURA < 1){
        $msg_erro[] = "Altura inválida: ".$ALTURA;
    }
    if($LARGURA < 1){
        $msg_erro[] = "Largura inválida: ".$LARGURA;
    }
    if($COMPRIMENTO < 1){
        $msg_erro[] = "Comprimento inválido: ".$COMPRIMENTO;
    }
    if($PESO_LIQUIDO < 1){
        $msg_erro[] = "Peso inválido: ".$PESO_LIQUIDO;
    }
    if(empty($LINK)){
        $msg_erro[] = "Link inválido: ".$LINK;
    }
    if(empty($rowImg['NOME_IMAGEM'])){
        $msg_erro[] = "Produto sem imagem: é preciso ter imagem";
    }
    if(count($rowPreco) < 1){
        $msg_erro[] = "Produto sem preço: é preciso ao menos 1 valor";
    }
    if($rowPreco['PRECO'] < 1){
        $msg_erro[] = "O preço do proudto deve ser maior que 0: ".substr($rowPreco['PRECO'], 0, -2);
    }
    if($QUANTIDADE < 1){
        $msg_erro[] = "Produto sem quantidade: é preciso ao menos 1 unidade";
    }
    if($_SESSION['LOJA_VIRTUAL'] < 1){
        $msg_erro[] = "O sistema precisa ser do tipo loja virtual";
    }
    
    // Campos Opcionais
    $MARCA           = empty($MARCA)          ? NULL : '"Marca": "'.$MARCA.'",';
    $GRUPO_CONTROLE  = empty($GRUPO_CONTROLE) ? NULL : '"groupId": "'.$GRUPO_CONTROLE.'",';
    $CODIGO_BARRA    = empty($CODIGO_BARRA)   ? NULL : '"barcode": "'.$CODIGO_BARRA.'",';
    $DESCRICAO_LOJA  = empty($DESCRICAO_LOJA) ? NULL : '"description": "'.removeLn($DESCRICAO_LOJA).'",';
    $GRUPO_NOME      = empty($GRUPO_NOME)     ? NULL : '"Ambiente": "'.$GRUPO_NOME.'",';
    $ITENS_INC       = empty($str_itens)      ? NULL : '"Itens Inclusos": "'.$str_itens.'",';
    
    if($sincronizar == true && count($msg_erro) < 1){
        // JSON default Cadastrar/Alterar
        $json = '[
            {
                '.$GRUPO_CONTROLE.'
                "sku": "'.$PRODUTO_CONTROLE.'",
                "link": "'.$LINK.'",
                "title": "'.$NOME.'",
                '.$CODIGO_BARRA.'
                "category": "'.$row_RecordsetProduto['BUSCAPE_CATEGORIA'].'",
                '.$DESCRICAO_LOJA.'
                "images": [
                    "'.$caminho.$rowImg['NOME_IMAGEM'].'"
                ],
                "quantity": '.substr($QUANTIDADE, 0 ,-4).',
                "technicalSpecification": {
                    '.$MARCA.'
                    '.$GRUPO_NOME.'
                    '.$ITENS_INC.'
                    "Aviso": "Imagem Ilustrativa"
                },
                "prices": [
                    {
                        "type": "boleto",
                        "price": '.substr($rowPreco['PRECO'], 0, -2).',
                        "installment": 1,
                        "installmentValue": '.substr($rowPreco['PRECO'], 0, -2).'
                    },
                    {
                        "type": "cartao_avista",
                        "price": '.substr($rowPreco['PRECO'], 0, -2).',
                        "installment": 1,
                        "installmentValue": '.substr($rowPreco['PRECO'], 0, -2).'
                    }
                ],
                "sizeHeight": '.$ALTURA.',
                "sizeLength": '.$COMPRIMENTO.',
                "sizeWidth": '.$LARGURA.',
                "weightValue": '.$PESO_LIQUIDO.',
                "marketplace": false
            }
        ]';
        
    }
}else{
    $BUSCAPE_PRODUTO_CONTROLE = 0;	
}


function salvar_cadastro($BUSCAPE_PRODUTO_CONTROLE,$PRODUTO_CONTROLE,$VARIANTE_CONTROLE,$CODIGO,$LINK,$BUSCAPE_CATEGORIA) {

    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);

    if ($BUSCAPE_PRODUTO_CONTROLE != 0) {

        $c_query = "UPDATE BUSCAPE_PRODUTO SET ";
        $c_query .= " BUSCAPE_CATEGORIA = '$BUSCAPE_CATEGORIA',";
        $c_query .= " LINK = '$LINK'           ";
        $c_query .= " WHERE BUSCAPE_PRODUTO_CONTROLE = '$BUSCAPE_PRODUTO_CONTROLE' ";
        $res = mysql_query($c_query,$con);
    }else{

        $c_query = "INSERT INTO BUSCAPE_PRODUTO SET            ";
        $c_query .= " PRODUTO_CONTROLE  = $PRODUTO_CONTROLE,   ";
        $c_query .= " VARIANTE_CONTROLE = $VARIANTE_CONTROLE,  ";
        $c_query .= " CODIGO            = '$CODIGO',           ";
        $c_query .= " BUSCAPE_CATEGORIA = '$BUSCAPE_CATEGORIA',";
        $c_query .= " LINK              = '$LINK',             ";
        $c_query .= " DT_CADASTRO       = '".date('Y-m-d')."'  ";
        $res = mysql_query($c_query,$con);

        $c_query2 = "SELECT MAX(BUSCAPE_PRODUTO_CONTROLE) AS TOTAL FROM BUSCAPE_PRODUTO ";
        $res = mysql_query($c_query2, $con);
        $row = mysql_fetch_assoc($res);

        $BUSCAPE_PRODUTO_CONTROLE = $row['TOTAL'];
    }

    return $BUSCAPE_PRODUTO_CONTROLE;

}
  
//FUNÇÃO PARA BUSCA DADOS RELACIONADOS AO PRODUTO PARA INSERÇÃO DO PRODUTO COMO ITEM NA NOTA OU PEDIDO
function busca_produto( $CODIGO) 
{	

  $con = mysql_connect( $GLOBALS[ 'hostname_hhsystem' ], $GLOBALS[ 'username_hhsystem' ], $GLOBALS[ 'password_hhsystem' ] );
      mysql_select_db( $GLOBALS[ 'database_hhsystem' ] );

      $c_query  = "SELECT P.PRODUTO_CONTROLE, 
                          P.NOME, 
                                              PC.CODIGO, 
                                              P.TIPO_LANCAMENTO_VENDA,
                                              P.QUANTIDADE_MULTIPLO, 
                                              PC.PRODUTOVARIANTE_CONTROLE, 
                                              P.ESTOQUE_UNIDADE_CONTROLE 
                                 FROM PRODUTOCODIGO PC ";
      $c_query .= "  LEFT JOIN PRODUTO P ON P.PRODUTO_CONTROLE = PC.PRODUTO_CONTROLE ";
      $c_query .= "  WHERE PC.CODIGO = '{$CODIGO}'
                     AND PC.STATUS = '1'
                     AND P.STATUS = '1' ";

      $res       = mysql_query( $c_query, $con ) or die( mysql_error( ) );
      $row       = mysql_fetch_assoc( $res );	
      $totalRows = mysql_num_rows( $res );	

      $PRODUTO_CONTROLE         = $row['PRODUTO_CONTROLE'];
      $PRODUTO_NOME             = $row['NOME'];
      $PRODUTOVARIANTE_CONTROLE = $row['PRODUTOVARIANTE_CONTROLE' ];
      
      if ( $PRODUTO_CONTROLE != 0 )
	  {
		$c_query3 = "SELECT TD.NOME, 
		                    PV.PRODUTOPRECO_CONTROLE,
                                    TD.TIPODETALHE_CONTROLE
					    FROM PRODUTOVARIANTEDETALHE PVD ";
		$c_query3 .= "  LEFT JOIN PRODUTOVARIANTE PV ON PV.PRODUTOVARIANTE_CONTROLE = PVD.PRODUTOVARIANTE_CONTROLE ";
		$c_query3 .= "  LEFT JOIN TIPODETALHE     TD ON TD.TIPODETALHE_CONTROLE     = PVD.TIPODETALHE_CONTROLE ";
		$c_query3 .= "  LEFT JOIN PRODUTOPRECO    PP ON PP.PRODUTOPRECO_CONTROLE    = PV.PRODUTOPRECO_CONTROLE ";	
		$c_query3 .= "  WHERE PV.PRODUTO_CONTROLE = '$PRODUTO_CONTROLE' 
                                                AND PVD.PRODUTOVARIANTE_CONTROLE = '$PRODUTOVARIANTE_CONTROLE' 
						AND PV.STATUS = '1' 
						AND PVD.STATUS = '1'
						AND PP.STATUS = '1'";
		
		$res3         = mysql_query( $c_query3, $con ) or die( mysql_error( ) );
		$row3         = mysql_fetch_assoc( $res3 );
		$totalRows3   = mysql_num_rows( $res3 );
                
                $result = array(
                    'produto_controle' => $PRODUTO_CONTROLE,
                    'produto_nome' => trim( strtr($PRODUTO_NOME, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC") ),
                    'variante_controle' => $PRODUTOVARIANTE_CONTROLE,
                    'tipodetalhe_controle' => $row3['TIPODETALHE_CONTROLE'],
                    'variante_nome' => trim( strtr($row3['NOME'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC") ),
                );
                
                return json_encode($result);
          }else{
                $result = array(
                    'produto_controle' => $PRODUTO_CONTROLE,
                    'produto_nome' => trim( strtr($PRODUTO_NOME, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC") ),
                );
          }

          
          
      return json_encode($result);
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
<script language="javascript" src="buscape_detalhes.js?v=9988YUU"></script>
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


<script>
  jQuery(document).ready(function()
  {
		$( "#DESCRICAO_4" ).combogrid(
		{
		   url: '../complete/server.php',
		   debug:true,
           //replaceNull: true,
		   colModel: [{'columnName':'id','width':'10','label':'id'},{'columnName':'codigo','width':'30','label':'codigo'}, {'columnName':'name','width':'60','label':'nome'}],
		   select: function( event, ui ) {
		   $( "#DESCRICAO_4" ).val( ui.item.name );
		   $( "#CODIGO_4" ).val( ui.item.codigo );
		   $( "#PRODUTO_CONTROLE" ).val( ui.item.id );
		   busca_codigo('4');
		   return false;
		}						
     });
  });
</script>


<body>   
  <form action="" method="post" name="form_detalhe" id="form_detalhe">
      <input name="BUSCAPE_PRODUTO_CONTROLE" type="hidden" id="BUSCAPE_PRODUTO_CONTROLE" value="<?php echo !is_null($BUSCAPE_PRODUTO_CONTROLE) ? $BUSCAPE_PRODUTO_CONTROLE : ''; ?>">
      <input name="PRODUTO_CONTROLE" type="hidden" class="campo" id="PRODUTO_CONTROLE" value="" size="10" maxlength="100">
      <input name="VARIANTE_CONTROLE" type="hidden" class="campo" id="VARIANTE_CONTROLE" value="" size="10" maxlength="100">
      <P>&nbsp;</P>  
      <table width="1180px" align="center" valign="top" style="position:relative; top:-10px">
          <tr valign="top">
            <td style="position:absolute; width:85%">
            <!--Integração Begin-->
            <?php if($sincronizar){ ?>
                <table width="85%" align="left" class="grade_dados_nota">
                <tr>
                  <td width="20"></td>
                  <td colspan="3"><strong>CADASTRO BUSCAPÉ</strong></td>
                </tr>
                <tr>
                  <td colspan="4">
                  <?php
                  
                  if(file_exists('https://www.google.com/analytics/images/GA_Home_Hero_01_dashboard.png')){
                      echo 'TRUE';
                  }else{
                      echo 'FALSE';
                  }
                  
                  if(count($msg_erro) < 1){
                    echo "<pre>";
                    echo $json;
                    echo "</pre>";
                  }else{
                  ?>
                <tr>
                  <td width="20"></td>
                  <td colspan="3">
                  <?php
                    foreach ($msg_erro as $value):
                        echo "<li style=\"color:red\">{$value}</li>";
                    endforeach;
                  ?>
                  </td>
                </tr>  
                  <?php
                      
                  }
                  ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="4">&nbsp;</td>
                </tr>
              </table>
            <?php } ?>
            <!--Integração End-->
            <?php if(!$visualizar && !$sincronizar && $BUSCAPE_PRODUTO_CONTROLE == 0){ ?>
              <!--TELA CADASTRO-->                                      
              <table width="85%" align="left" class="grade_dados_nota">
                <tr>
                  <td width="20"></td>
                  <td colspan="3"><strong>CADASTRO BUSCAPÉ</strong></td>
                </tr>
                <tr>
                  <td colspan="4">&nbsp;</td>
                </tr>
                <tr>
                  <td width="20"></td>
                  <td width="100"><strong>Código</strong></td>
                  <td width="220"><strong>Descrição</strong></td>
                  <td width="300"><strong>Link</strong></td>
                </tr>
                <tr>
                  <td width="20"></td>
                  <td><input name="CODIGO_4" style="width:90px" type="text" id="CODIGO_4" size="14" maxlength="14" tabindex="26" onfocus="seleciona_conteudo('CODIGO_4');focar_campo(true)" onkeypress="limparcampos('4')"></td>
                  <td><input onFocus="seleciona_conteudo('DESCRICAO_4');busca_codigo(4)" onChange="busca_codigo(4)" name="DESCRICAO_4" style="width:100%" type="text" id="DESCRICAO_4"  onkeypress="return pulacampo(this, event)"  tabindex="27" onFocus="focar_campo(false)"/></td>
                  <td><input name="LINK" type="text" class="campo" id="LINK" value="" style="width:100%"></td>
                </tr>
                <tr>
                  <td colspan="4"><select name="BUSCAPE_CATEGORIA" id="BUSCAPE_CATEGORIA" style="display: none;"><option value=""></option></select></td>
                </tr>
                <tr>
                  <td width="20"></td>
                  <td colspan="3">
                    <input type="button" name="button" value="Salvar dados" onClick="if (!!menu_novo('<?php echo $_SESSION['usr_pode_banco'];?>','I','A',document.getElementById('BUSCAPE_PRODUTO_CONTROLE').value)){ salvar_cadastro(); }" style="cursor:pointer;" >
                  </td>
                </tr>                              
              </table>
                <!--/TELA CADASTRO-->
                <?php } ?>
                
                <?php if(!$visualizar && !$sincronizar && $BUSCAPE_PRODUTO_CONTROLE != 0){ ?>
                <!--TELA ALTERAR CADASTRO-->  
                <table width="85%" align="left" class="grade_dados_nota">
                  <tr>
                    <td width="20"></td>
                    <td colspan="3"><strong>ALTERAR CADASTRO BUSCAPÉ</strong></td>
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
                  <?php if(!$xml){?>
                  <tr>
                      <td width="20"></td>
                      <td colspan="3" style="color:red;">Buscapé API Category List não responde.</td>
                  </tr>
                  <?php }else{ ?>
                  <tr>
                    <td width="20"></td>
                    <td width="100">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="146" colspan="4">
                        <p><strong>Grupo:</strong> <?php echo $GRUPO_NOME_FORM; ?></p>
                        <p><strong>Pesquisa:</strong> <?php echo $categoria_pesquisa; ?></p>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="4">
                        <select name="BUSCAPE_CATEGORIA" class="campo" id="BUSCAPE_CATEGORIA">
                        <?php
                        echo "<option value=\"\"></option>";
                        foreach($array_cateogiras as $value):
                            $select = $row_RecordsetProduto['BUSCAPE_CATEGORIA'] == $value ? 'selected' : NULL;
                            echo "<option value=\"".$value."\" {$select}>".$value."</option>";
                        endforeach;
                        ?>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <td width="20"></td>
                    <td colspan="3">
                      <input name="BUSCAPE_PRODUTO_CONTROLE" id="BUSCAPE_PRODUTO_CONTROLE" type="hidden" value="<?php echo $BUSCAPE_PRODUTO_CONTROLE; ?>">
                      <input type="button" name="button" value="Salvar dados" onClick="if (!!menu_novo('<?php echo $_SESSION['usr_pode_banco'];?>','I','A',document.getElementById('BUSCAPE_PRODUTO_CONTROLE').value)){ salvar_cadastro(); }" style="cursor:pointer;" >
                    </td>
                  </tr>
                  <?php }?>
                </table>
                <!--/TELA ALTERAR CADASTRO-->
                <?php }?>
                <?php if($visualizar){ ?>
                <!--WS Info Begin-->
                <table width="85%" align="left" class="grade_dados_nota">
                    <tr>
                      <td width="100"><strong>Status Publicação:</strong></td>
                      <td width="100"><strong>Data criação:</strong></td>
                      <td width="100"><strong>Data atualização:</strong></td>
                      <td width="100"><strong>Grupo ID:</strong></td>
                      <td width="90"><strong>ID:</strong></td>
                    </tr>
                    <tr>
                      <td valign="top"><?php echo utf8_decode($ws_ret->summary->status); ?></td>
                      <td valign="top"><?php echo $ws_ret->summary->creationDate; ?></td>
                      <td valign="top"><?php echo $ws_ret->summary->updateDate; ?></td>
                      <td valign="top"><?php echo $ws_ret->productDataSent->groupId; ?></td>
                      <td valign="top"><?php echo $ws_ret->productDataSent->sku; ?></td>
                    </tr>
                    <tr>
                      <td width="100" colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="100" colspan="2"><strong>Título:</strong></td>
                      <td width="100"><strong>Código de Barras:</strong></td>
                      <td width="100"><strong>Categoria:</strong></td>
                      <td width="100"><strong>Descrição</strong></td>
                    </tr>
                    <tr>
                      <td valign="top" colspan="2"><?php echo utf8_decode($ws_ret->productDataSent->title); ?></td>
                      <td valign="top"><?php echo utf8_decode($ws_ret->productDataSent->barcode); ?></td>
                      <td valign="top"><?php echo utf8_decode($ws_ret->productDataSent->category); ?></td>
                      <td valign="top"><?php echo utf8_decode($ws_ret->productDataSent->description); ?></td>
                    </tr>
                    <tr>
                      <td width="100" colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="100" colspan="6"><strong>Link:</strong></td>
                    </tr>
                    <tr>
                      <td valign="top" colspan="6"><?php echo $ws_ret->productDataSent->link; ?></td>                   
                    </tr>
                    <tr>
                      <td width="100" colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="100" colspan="6"><strong>Imagens:</strong></td>
                    </tr>
                    <tr>
                      <td valign="top" colspan="6">
                          <ul>
                              <?php
                              foreach($ws_ret->productDataSent->images as $value):
                                  $i = $i + 1;
                                  echo "<li><strong>Imagem[{$i}]:</strong> {$value}</li>";
                              endforeach;
                              ?>
                          </ul>    
                      </td>
                    </tr>
                    <tr>
                      <td width="100" colspan="6">&nbsp;</td>
                  </tr>
                    <tr>
                      <td width="100" colspan="6"><strong>Preços:</strong></td>
                    </tr>
                    <tr>
                      <td valign="top" colspan="6">
                          <ul>
                              <?php
                              foreach($ws_ret->productDataSent->prices as $value):
                                  echo "<li><strong>{$value->type}:</strong> ".$value->price."</li>";
                              endforeach;
                              ?>
                          </ul>    
                      </td>
                    </tr>
                    <tr>
                      <td width="100" colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="100"><strong>Tipo:</strong></td>
                      <td width="100"><strong>Marca:</strong></td>
                      <td width="100" colspan="3"><strong>Montagem:</strong></td>
                      <td width="100" colspan="1"><strong>Aviso:</strong></td>
                    </tr>
                    <tr>
                      <td valign="top"><?php echo utf8_decode($ws_ret->productDataSent->technicalSpecification->Tipo); ?></td>
                      <td valign="top"><?php echo utf8_decode($ws_ret->productDataSent->technicalSpecification->Marca); ?></td>
                      <td valign="top" colspan=3"><?php echo utf8_decode($ws_ret->productDataSent->technicalSpecification->Montagem); ?></td>                
                      <td valign="top" colspan="1"><?php echo utf8_decode($ws_ret->productDataSent->technicalSpecification->Aviso); ?></td>                
                    </tr>
                    <tr>
                      <td width="100" colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="100"><strong>Quantidade:</strong></td>
                      <td width="100"><strong>Altura:</strong></td>
                      <td width="100"><strong>Comprimento:</strong></td>
                      <td width="100"><strong>Largura:</strong></td>
                      <td width="100"><strong>Peso:</strong></td>
                    </tr>
                    <tr>
                      <td valign="top"><?php echo $ws_ret->productDataSent->quantity; ?></td>
                      <td valign="top"><?php echo $ws_ret->productDataSent->sizeHeight; ?></td>
                      <td valign="top"><?php echo $ws_ret->productDataSent->sizeLength; ?></td>
                      <td valign="top"><?php echo $ws_ret->productDataSent->sizeWidth; ?></td>                
                      <td valign="top"><?php echo $ws_ret->productDataSent->weightValue; ?></td>                
                    </tr>
                    <tr>
                      <td width="100" colspan="6">&nbsp;</td>            
                    </tr>
                </table>
                <!--WS Info Begin-->
                <?php }?>
            </td>
            
            <input type="hidden" name="LINHA_" id="LINHA_" value="1" />
            <input type="hidden" name="CODIGOBARRA" id="CODIGOBARRA" value="" />
    

          </tr>          
        </table>
     </form>
   
    
  </body>
</html>