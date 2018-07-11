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
$query_RecordsetProduto .= " FROM HUB2B_PRODUTO AS BP ";
$query_RecordsetProduto .= " LEFT JOIN PRODUTO             P   ON BP.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
$query_RecordsetProduto .= " LEFT JOIN PRODUTOCODIGO       PC  ON PC.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
$query_RecordsetProduto .= " LEFT JOIN PRODUTOLOCALESTOQUE PLE ON PC.PRODUTO_CONTROLE = PLE.PRODUTO_CONTROLE AND PC.PRODUTOVARIANTE_CONTROLE = PLE.PRODUTOVARIANTE_CONTROLE ";
$query_RecordsetProduto .= " LEFT JOIN MARCA               MA  ON MA.MARCA_CONTROLE   = P.MARCA_CONTROLE ";
$query_RecordsetProduto .= " LEFT JOIN PRODUTOGRUPO        PG  ON PG.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
$query_RecordsetProduto .= " LEFT JOIN GRUPO               GR  ON GR.GRUPO_CONTROLE   = PG.GRUPO_CONTROLE ";
$query_RecordsetProduto .= " WHERE BP.HUB2B_PRODUTO_CONTROLE =  '$id_controle' ";
$RecordsetProduto = mysql_query($query_RecordsetProduto, $hhsystem) or die(mysql_error());
$row_RecordsetProduto = mysql_fetch_assoc($RecordsetProduto);
$totalRows_RecordsetProduto = mysql_num_rows($RecordsetProduto);

if ($totalRows_RecordsetProduto != 0 ) {
    $HUB2B_PRODUTO_CONTROLE      = $row_RecordsetProduto['HUB2B_PRODUTO_CONTROLE'];
    $PRODUTO_CONTROLE            = $row_RecordsetProduto['PRODUTO_CONTROLE'      ];
    $CODIGO_BARRA                = $row_RecordsetProduto['CODIGO_BARRA'          ];
    $CODIGO                      = $row_RecordsetProduto['CODIGO'                ];
    $DESCRICAO_LOJA              = $row_RecordsetProduto['DESCRICAO_LOJA'        ];
    $NOME                        = $row_RecordsetProduto['NOME'                  ];
    $GRUPO_NOME                  = $row_RecordsetProduto['GRUPO_NOME'            ];
    $GRUPO_NOME_FORM             = $row_RecordsetProduto['GRUPO_NOME'            ];
    $ALTURA                      = round($row_RecordsetProduto['ALTURA'] * 100   );
    $LARGURA                     = round($row_RecordsetProduto['LARGURA'] * 100  );
    $COMPRIMENTO                 = round($row_RecordsetProduto['COMPRIMENTO'] * 100);
    $PESO_LIQUIDO                = $row_RecordsetProduto['PESO_LIQUIDO'] * 1000;
    $LINK                        = $row_RecordsetProduto['LINK'                  ];
    $MARCA                       = $row_RecordsetProduto['NOME_MARCA'            ];
    $GRUPO_CONTROLE              = $row_RecordsetProduto['GRUPO_CONTROLE'        ];
    $QUANTIDADE                  = $row_RecordsetProduto['QUANTIDADE'            ];
   
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
    $HUB2B_PRODUTO_CONTROLE = 0;	
}

/*
 * Lista todos os marketplaces
 */
function get_marketplace(){
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $qry = "SELECT * FROM HUB2B_MARKETPLACE";
    $res = mysql_query($qry,$con);
    $row = mysql_fetch_assoc($res);
    
    $arr = array();
    do{
       $arr[] = array('id' => $row['HUB2B_MARKETPLACE_CONTROLE'], 'nome' => $row['NOME']);
    } while($row = mysql_fetch_assoc($res));
    
    return $arr;
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

function insert_produto_marketplace($HUB2B_CONTROLE, $MARKET_CONTROLE){
    
    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    $c_query = "INSERT INTO HUB2B_PRODUTO_MARKETPLACE SET       ";
    $c_query .= " HUB2B_PRODUTO_CONTROLE     = $HUB2B_CONTROLE, ";
    $c_query .= " HUB2B_MARKETPLACE_CONTROLE = $MARKET_CONTROLE ";
    $res = mysql_query($c_query,$con);
    
}

function salvar_cadastro($HUB2B_PRODUTO_CONTROLE, $PRODUTO_CONTROLE, $VARIANTE_CONTROLE, $CODIGO, $LINK, $MARKETPLACE) {

    $con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);
    
    if ($HUB2B_PRODUTO_CONTROLE != 0) {

        # Atualiza o endereco da pagina do produto
        $c_query = "UPDATE HUB2B_PRODUTO SET                    ";
        $c_query .= " LINK               = '$LINK'              ";
        $c_query .= " WHERE HUB2B_PRODUTO_CONTROLE = '$HUB2B_PRODUTO_CONTROLE' ";
        $res = mysql_query($c_query,$con);
        
        # Limpa registro(s) do produto
        $q_del = "DELETE FROM HUB2B_PRODUTO_MARKETPLACE ";
        $q_del .= " WHERE HUB2B_PRODUTO_CONTROLE = '$HUB2B_PRODUTO_CONTROLE' ";
        $del_r = mysql_query($q_del,$con);
        
        # Registra produto marketplace
        $dados = explode(",", $MARKETPLACE);
        if(count($dados) > 0){
            foreach($dados as $value):
                insert_produto_marketplace($HUB2B_PRODUTO_CONTROLE, $value);
            endforeach;
        }
        
    }else{

        $c_query = "INSERT INTO HUB2B_PRODUTO SET                 ";
        $c_query .= " PRODUTO_CONTROLE  = $PRODUTO_CONTROLE,      ";
        $c_query .= " VARIANTE_CONTROLE = $VARIANTE_CONTROLE,     ";
        $c_query .= " CODIGO            = '$CODIGO',              ";
        $c_query .= " LINK              = '$LINK',                ";
        $c_query .= " DT_CADASTRO       = '".date('Y-m-d')."'     ";
        $res = mysql_query($c_query,$con);
        $id  = mysql_insert_id($con);

        # Registra produto marketplace
        $dados = explode(",", $MARKETPLACE);
        if(count($dados) > 0){
            foreach($dados as $value):
                insert_produto_marketplace($id, $value);
            endforeach;
        }

        $c_query2 = "SELECT MAX(HUB2B_PRODUTO_CONTROLE) AS TOTAL FROM HUB2B_PRODUTO ";
        $res = mysql_query($c_query2, $con);
        $row = mysql_fetch_assoc($res);

        $HUB2B_PRODUTO_CONTROLE = $row['TOTAL'];
    }

    return $HUB2B_PRODUTO_CONTROLE;

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
<title>Cadastro Hub2b</title>
<script type="text/javascript" src="../../hhsystem/funcoes/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../../hhsystem/funcoes/jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../style/jquery.fancybox.css?v=2.1.6" media="screen">
<script language="javascript" src="../../hhsystem/funcoes/funcoes.js"></script>
<script language="javascript" src="hub2b_detalhes.js?v=113"></script>
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
      <input name="HUB2B_PRODUTO_CONTROLE" type="hidden" id="HUB2B_PRODUTO_CONTROLE" value="<?php echo !is_null($HUB2B_PRODUTO_CONTROLE) ? $HUB2B_PRODUTO_CONTROLE : ''; ?>">
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
                  <td colspan="3"><strong>CADASTRO HUB2B</strong></td>
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
            <?php if(!$visualizar && !$sincronizar && $HUB2B_PRODUTO_CONTROLE == 0){ ?>
              <!--TELA CADASTRO-->                                      
              <table width="85%" align="left" class="grade_dados_nota">
                <tr>
                  <td width="20"></td>
                  <td colspan="3"><strong>CADASTRO HUB2B</strong></td>
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
                <td width="20"></td>
                <td width="100" colspan="4">
                    <table>
                    <?php 
                    $i = 0;
                    echo "<tr>";
                    foreach(get_marketplace() as $value): 
                        echo "<td><label id=\"checkMarketplace\"><input type=\"checkbox\" name=\"marketplace[]\" id=\"marketplace\" value=\"".$value['id']."\"> ".$value['nome']."</label></td>";
                        ++$i;
                        if($i % 5 == 0){
                            echo "</tr><tr>";
                        }
                    endforeach;
                    echo "</tr>";
                    ?>
                    </table>
                </td>
              </tr>
                <tr>
                  <td colspan="4"><select name="BUSCAPE_CATEGORIA" id="BUSCAPE_CATEGORIA" style="display: none;"><option value=""></option></select></td>
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
                    <td width="100"><strong>Categoria</strong></td>
                    <td width="50"><strong>Status SKU</strong></td>
                    <td width="300"></td>
                  </tr>
                  <tr>
                    <td width="20"></td>
                    <td><?php echo $GRUPO_NOME_FORM; ?></td>
                    <td>
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                      <td colspan="4">&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="20"></td>
                    <td width="100" colspan="4">
                        <table>
                        <?php 
                        $hub_mkt = get_porduto_marketplace($HUB2B_PRODUTO_CONTROLE);
                        $i = 0;
                        echo "<tr>";
                        foreach(get_marketplace() as $value): 
                            $check = in_array($value['id'], $hub_mkt) ? "checked" : NULL;
                            echo "<td><label id=\"checkMarketplace\"><input type=\"checkbox\" name=\"marketplace[]\" id=\"marketplace\" value=\"".$value['id']."\" {$check}> ".$value['nome']."</label></td>";
                            ++$i;
                            if($i % 5 == 0){
                                echo "</tr><tr>";
                            }
                        endforeach;
                        echo "</tr>";
                        ?>
                        </table>
                    </td>
                  </tr>
                  <tr>
                    <td width="146" colspan="4">
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