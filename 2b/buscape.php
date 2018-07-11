<?php


session_start();
require_once('../Connections/hhsystem.php');
include('../admin/restrito.php');
require_once('../../hhsystem/funcoes/funcoes.php');
require_once('../admin/geral.php');
include('../admin/config.php');
require_once('../../hhsystem/funcoes/Sajax.php');

$pagina = $_GET['pagina']; 
if (!$pagina) { 
$iPagina = "1"; 
} else { 
$iPagina = $pagina; 
}
$total_reg = 50;
$inicio = $iPagina - 1; 
$inicio = $inicio * $total_reg;

if (isset($_GET['nome'])) {
  $nome = $_GET['nome'];
} else {
  $nome = '';
}


if (isset($_GET['codigo'])) {
  $codigo = $_GET['codigo'];
} else {
  $codigo = '';
}

if (isset($_GET['ordem'])) {
  $campo_ordem = $_GET['ordem'];
  $by = ($campo_ordem != 'PRODUTO_CONTROLE' ? 'ASC' : 'DESC');
} else {
  $by = 'DESC';
  $campo_ordem = 'PRODUTO_CONTROLE';
}

function trata_texto($string){
    return trim(strtr($string, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC"));
}

# Query para realizar a aginação
mysql_select_db($database_hhsystem, $hhsystem);
$query_RecordsetResumoTotal = "SELECT BP.*,"
                                  . " P.NOME,"
                                  . " P.ESTOQUE_UNIDADE_CONTROLE";
$query_RecordsetResumoTotal .= " FROM BUSCAPE_PRODUTO AS BP ";
$query_RecordsetResumoTotal .= " LEFT JOIN PRODUTO   P ON BP.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
$query_RecordsetResumoTotal .= " WHERE BP.BUSCAPE_PRODUTO_CONTROLE > 0 ";
if ($nome <> ''){
$query_RecordsetResumoTotal .= "AND P.NOME LIKE '%$nome%' " ;
}
if ($codigo <> ''){
$query_RecordsetResumoTotal .= "AND BP.BUSCAPE_PRODUTO_CONTROLE = '$codigo' " ;
}
$RecordsetResumoTotal = mysql_query($query_RecordsetResumoTotal, $hhsystem) or die(mysql_error());
$row_RecordsetResumoTotal = mysql_fetch_assoc($RecordsetResumoTotal);
$totalRows_RecordsetResumoTotal = mysql_num_rows($RecordsetResumoTotal);

# Query para realizar a listagem
$query_RecordsetResumo = "SELECT BP.*,"
                             . " P.NOME,"
                             . " PC.CODIGO AS VARIANTE_CODIGO,"
                             . " TD.NOME AS DETALHE_NOME,"
                             . " PLE.SALDO AS ESTOQUE_UNIDADE_CONTROLE ";
$query_RecordsetResumo .= " FROM BUSCAPE_PRODUTO AS BP ";
$query_RecordsetResumo .= " LEFT JOIN PRODUTOLOCALESTOQUE        PLE ON BP.PRODUTO_CONTROLE = PLE.PRODUTO_CONTROLE AND BP.VARIANTE_CONTROLE = PLE.PRODUTOVARIANTE_CONTROLE ";
$query_RecordsetResumo .= " LEFT JOIN PRODUTO                    P   ON BP.PRODUTO_CONTROLE = P.PRODUTO_CONTROLE ";
$query_RecordsetResumo .= " LEFT JOIN PRODUTOCODIGO              PC  ON PC.CODIGO = BP.CODIGO";
$query_RecordsetResumo .= " LEFT JOIN PRODUTOVARIANTE            PVC ON PVC.PRODUTOVARIANTE_CONTROLE =  BP.VARIANTE_CONTROLE ";
$query_RecordsetResumo .= " LEFT JOIN PRODUTOVARIANTEDETALHE     PVD ON PVD.PRODUTOVARIANTE_CONTROLE =  PVC.PRODUTOVARIANTE_CONTROLE ";
$query_RecordsetResumo .= " LEFT JOIN TIPODETALHE                TD  ON TD.TIPODETALHE_CONTROLE =  PVD.TIPODETALHE_CONTROLE ";
$query_RecordsetResumo .= " WHERE BP.BUSCAPE_PRODUTO_CONTROLE > 0 ";
if ($nome <> ''){
    $query_RecordsetResumo .= "AND P.NOME LIKE '%$nome%' ";
}
if ($codigo <> ''){
    $query_RecordsetResumo .= "AND P.PRODUTO_CONTROLE = '$codigo' ";
}
$query_RecordsetResumo .= "GROUP BY  BP.BUSCAPE_PRODUTO_CONTROLE ";
$query_RecordsetResumo .= "ORDER BY P.{$campo_ordem} {$by} ";
$query_RecordsetResumo .= "LIMIT $inicio,$total_reg ";

$RecordsetResumo = mysql_query($query_RecordsetResumo, $hhsystem) or die(mysql_error());
$row_RecordsetResumo = mysql_fetch_assoc($RecordsetResumo);
$totalRows_RecordsetResumo = mysql_num_rows($RecordsetResumo);


function excluir_cadastro($CONTROLE) {
	
	$con = mysql_connect($GLOBALS['hostname_hhsystem'],$GLOBALS['username_hhsystem'],$GLOBALS['password_hhsystem']);
    mysql_select_db($GLOBALS['database_hhsystem']);

	$c_query  = " DELETE FROM BUSCAPE_PRODUTO ";
	$c_query .= " WHERE BUSCAPE_PRODUTO_CONTROLE = '$CONTROLE' ";
	$res = mysql_query($c_query,$con);
	
	return '';

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

function sincronizarMassa($value)
{
    $debug  = FALSE;
    $array  = explode(",", $value);
    $result = array();
    
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
    $query_RecordsetProduto .= " WHERE BP.BUSCAPE_PRODUTO_CONTROLE =  '$value' ";
    $RecordsetProduto = mysql_query($query_RecordsetProduto, $con) or die(mysql_error());
    $row_RecordsetProduto = mysql_fetch_assoc($RecordsetProduto);
    $totalRows_RecordsetProduto = mysql_num_rows($RecordsetProduto);
    $result[] = $row_RecordsetProduto;
    endforeach;
    
    // Define o caminho da imagem
    $caminho = $_SESSION['CAMINHO_FOTO'];
    $caminho.  'userfiles/' . $PRODUTO_CONTROLE.  '/sistema/mini.jpg';
    
    # Monta o JSON para envio
    $string    = '';
    $count     = 0;
    $arr_curso = array();
    foreach ($result as $value):
        $BUSCAPE_PRODUTO_CONTROLE    =   $value['BUSCAPE_PRODUTO_CONTROLE'];
        $PRODUTO_CONTROLE            =   $value['PRODUTO_CONTROLE'];
        $CODIGO_BARRA                =   $value['CODIGO_BARRA'];
        $CODIGO                      =   $value['CODIGO'];
        $DESCRICAO_LOJA              =   $value['DESCRICAO_LOJA'];
        $NOME                        =   $value['NOME'];
        $GRUPO_NOME                  =   $value['GRUPO_NOME'];
        $GRUPO_NOME_FORM             =   $value['GRUPO_NOME'];
        $ALTURA                      =   round($value['ALTURA'] * 100);
        $LARGURA                     =   round($value['LARGURA'] * 100);
        $COMPRIMENTO                 =   round($value['COMPRIMENTO'] * 100);
        $PESO_LIQUIDO                =   $value['PESO_LIQUIDO'] * 1000;
        $LINK                        =   $value['LINK'];
        $MARCA                       =   $value['NOME_MARCA'];
        $GRUPO_CONTROLE              =   $value['GRUPO_CONTROLE'];
        $QUANTIDADE                  =   $value['QUANTIDADE'];
    
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
        
        // Validar erros no produto
        $msg_erro = array();
        if(empty($NOME)){
            $msg_erro[] = trata_texto("Nome inválido: ". $NOME);
        }
        if(!is_numeric($PRODUTO_CONTROLE)){
            $msg_erro[] = trata_texto("ID inválido: ". $PRODUTO_CONTROLE);
        }
        if(empty($row_RecordsetProduto['BUSCAPE_CATEGORIA'])){
            $msg_erro[] = trata_texto("Categoria inválida: informe uma categoria");
        }
        if($ALTURA < 1){
            $msg_erro[] = trata_texto("Altura inválida: ". $ALTURA);
        }
        if($LARGURA < 1){
            $msg_erro[] = trata_texto("Largura inválida: ". $LARGURA);
        }
        if($COMPRIMENTO < 1){
            $msg_erro[] = trata_texto("Comprimento inválido: ". $COMPRIMENTO);
        }
        if($PESO_LIQUIDO < 1){
            $msg_erro[] = trata_texto("Peso inválido: ". $PESO_LIQUIDO);
        }
        if(empty($LINK)){
            $msg_erro[] = trata_texto("Link inválido: ". $LINK);
        }
        if(empty($rowImg['NOME_IMAGEM'])){
            $msg_erro[] = trata_texto("Produto sem imagem: é preciso ter imagem");
        }
        if(count($rowPreco) < 1){
            $msg_erro[] = trata_texto("Produto sem preço: é preciso ao menos 1 valor");
        }
        if($rowPreco['PRECO'] < 1){
            $msg_erro[] = trata_texto("O preço do proudto deve ser maior que 0: ".substr($rowPreco['PRECO'], 0, -2));
        }
        if($QUANTIDADE < 1){
            $msg_erro[] = trata_texto("Produto sem quantidade: é preciso ao menos 1 unidade");
        }
        if($_SESSION['LOJA_VIRTUAL'] < 1){
            $msg_erro[] = trata_texto("O sistema precisa ser do tipo loja virtual");
        }
        
        if(count($msg_erro) > 0){
            $arr_curso[] = array(
                'titulo' => trata_texto($NOME),
                'erro'   => $msg_erro
            );
        }
        
        // Campos Opcionais 
        $MARCA           = empty($MARCA)          ? NULL : '"Marca": "'.$MARCA.'",';
        #$GRUPO_CONTROLE  = empty($GRUPO_CONTROLE) ? NULL : '"groupId": "'.$GRUPO_CONTROLE.'",';
        $CODIGO_BARRA    = empty($CODIGO_BARRA)   ? NULL : '"barcode": "'.$CODIGO_BARRA.'",';
        $DESCRICAO_LOJA  = empty($DESCRICAO_LOJA) ? NULL : '"description": "'.removeLn($DESCRICAO_LOJA).'",';
        $GRUPO_NOME      = empty($GRUPO_NOME)     ? NULL : '"Ambiente": "'.$GRUPO_NOME.'",';
        $ITENS_INC       = empty($str_itens)      ? NULL : '"Itens Inclusos": "'.$str_itens.'",';
        
        $string .= '{
                "groupId": "'.$GRUPO_CONTROLE.'",
                "sku": "'.$PRODUTO_CONTROLE.'",
                "link": "'.$LINK.'",
                "title": "'.$NOME.'",
                '.$CODIGO_BARRA.'
                "category": "'.$value['BUSCAPE_CATEGORIA'].'",
                '.$DESCRICAO_LOJA.'
                "images": [
                    "'.$caminho.$rowImg['NOME_IMAGEM'].'"
                ],
                "quantity": '.substr($QUANTIDADE, 0, -4).',
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
            }';
        $count++;
        $string .= $count < count($result) ? ',' : '';
    endforeach;
    
    # Proteção para quantidade mínima de produtos
    if($count < 20){
        $rst = json_encode(array('status' => false, 'msg' => "A quantidade deve ser de 20 a 1k!", 'options' => NULL));
        return $rst;
    }
    
    # Checa se houve erro nos dados do produto
    if(count($arr_curso) > 0){
        $rst = json_encode(array('status' => false, 'msg' => "Erro nos dados do(s) produto(s):", "options" => $arr_curso));
        return $rst;
    }
    
    $json = trata_texto("[$string]");    
    $context_options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-type: application/json\r\n"
                    . "charset: UTF-8\r\n"
                    . "User-Agent: PHP\r\n"
                    . "app-token: 41fa5164-493c-3949-b828-8563feafc367\r\n"
                    . "Connection: Connection: Keep-Alive\r\n"
                    . "Content-Length: " . strlen($json) . "\r\n",
                    'content' => $json
                )
    );
    $context = stream_context_create($context_options);
    $ws_rst = file_get_contents('http://sandbox-api.buscape.com.br/product/t1/collection', false, $context);
    $ws_ret = json_decode($ws_rst);
    
    # Verifica se o WS Buscape retornou um array
    if(is_array($ws_ret)){
        foreach ($ws_ret as $value):
            $status = $value->status == 'SUCCESS' ? 200 : 400;
            $c_query = "UPDATE BUSCAPE_PRODUTO SET ";
            $c_query .= " STATUS_CODE = '$status'           ";
            $c_query .= " WHERE PRODUTO_CONTROLE = '{$value->sku}' ";
            $res = mysql_query($c_query,$con);
        endforeach;
        $rst = array('status' => true, 'msg' => 'Sincronizado com sucesso!');
    }else{
        $rst = array('status' => false,'msg' => 'Erro na requisição!');
    }
    print_r(json_encode($rst));
}


$sajax_request_type = "GET"; //forma como os dados serao enviados
sajax_init(); //inicia o SAJAX
sajax_export("sincronizarMassa","excluir_cadastro"); // lista de funcoes a ser exportadas
sajax_handle_client_request();// serve instancias de clientes

$_SESSION['titulo'] = 'Cadastro de Cfop';
include('../admin/header.php');

?>
<script language="javascript" src="buscape.js?v=1623"></script>
<script language="javascript"><!--
<?
sajax_show_javascript(); //gera o javascript
?>
//-->
</script>




<form action="" method="get" name="form2" id="form2">
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td width="400"><a class="fancybox fancybox.iframe" href="buscape_detalhes.php"><img src="../images/bt_novo_cadastro.jpg" width="131" height="30" border="0" /></a></td>
      <td width="400" align="right"><span class="titulo_pagina">Cadastro de produtos Buscapé</span></td>
    </tr>
    <tr>
      <td height="15"></td>
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
            <input type="checkbox" class="flat" name="exportar[]" value="<?php echo $row_RecordsetResumo['BUSCAPE_PRODUTO_CONTROLE']; ?>">
        </label>
      </td>
      <td align="center"><a class="fancybox fancybox.iframe" href="buscape_detalhes.php?cmd=<?php echo $row_RecordsetResumo['BUSCAPE_PRODUTO_CONTROLE']; ?>&sincronizar=1"><img src="../images/<?php echo $row_RecordsetResumo['STATUS_CODE'] != 200 ? '1.gif' : '2.gif'; ?>" width="16" height="16" border="0" title="Sincronizar produto"></a></td>
      <td align="center"><a class="fancybox fancybox.iframe" href="buscape_detalhes.php?cmd=<?php echo $row_RecordsetResumo['BUSCAPE_PRODUTO_CONTROLE']; ?>"><img src="../images/b_browse.png" width="16" height="16" border="0" title="Alterar produto"></a></td>
      <td align="center"><img src="../images/delete.gif" width="16" height="15" onclick="if (!!menu_novo('<?php echo $_SESSION['usr_pode_banco'];?>','E','','')){ excluir_cadastro(<?php echo $row_RecordsetResumo['BUSCAPE_PRODUTO_CONTROLE']; ?>) ;}" style="cursor:pointer;" title="Excluir produto" /></td>
    </tr>
    <?php 
		  $ii++;
		  } while ($row_RecordsetResumo = mysql_fetch_assoc($RecordsetResumo)); 
		  ?>
    <tr bgcolor="#F2F2F2">
        <td align="left">&nbsp; </td>
        <td align="left">&nbsp;</td>
        <td align="left">&nbsp;</td>
        <td align="left">&nbsp;</td>
        <td align="right" colspan="3">
            <input name="" type="button" value="Sincronizar" onclick="sincronizarMassa()">
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
<?php } 
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