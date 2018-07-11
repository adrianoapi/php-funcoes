
function salvar_cadastro() {

var HUB2B_PRODUTO_CONTROLE   = document.getElementById('HUB2B_PRODUTO_CONTROLE').value;
var PRODUTO_CONTROLE         = document.getElementById('PRODUTO_CONTROLE'      ).value;
var VARIANTE_CONTROLE        = document.getElementById('VARIANTE_CONTROLE'     ).value;
var LINK                     = document.getElementById('LINK'                  ).value;
var CODIGO                   = document.getElementById('CODIGO_4'              ).value;
var MARKETPLACE              = [];
    $('#checkMarketplace :checked').each(function () {
        MARKETPLACE.push($(this).val());
    });

x_salvar_cadastro(HUB2B_PRODUTO_CONTROLE, PRODUTO_CONTROLE, VARIANTE_CONTROLE, CODIGO, LINK, MARKETPLACE, volta_cadastro);

}

function volta_cadastro(cret) {
    alert('Informação gravada com sucesso.');
    document.getElementById('HUB2B_PRODUTO_CONTROLE').value = cret ;
    parent.$.fancybox.close();
}

function seleciona_conteudo( elemento )
{
	if ( document.getElementById( elemento ) )
	{
		if ( document.getElementById( elemento ).type == 'text' )
		{
		   document.getElementById( elemento ).setSelectionRange(0,30);
		}
	}
}

function busca_codigo( linha, unidade_controle, panel ) 
{
	if ( document.getElementById( 'panelItens2' ) && panel == undefined )
	{
		if ( document.getElementById( 'panelItens2' ).style.display == "" )
		{			
			tabItensselect( 2 );
		}
	}
	
	if ( unidade_controle == undefined )
	  unidade_controle = 0;
	  
	var codigo      = document.getElementById( 'CODIGO_'+ linha       ).value;
	var descricao   = document.getElementById( 'DESCRICAO_'+ linha    ).value;
	
	if ( linha > 0 )
	  document.getElementById( 'LINHA_' ).value = linha;	  	 
	
	if ( codigo.trim() != "" && descricao.trim() == "" || codigo.trim() != document.getElementById( 'CODIGOBARRA' ).value )
	{
		x_busca_produto( codigo, volta_produto );
	} else if ( codigo.trim() != "" && descricao.trim() != "" )
	{
					
	        x_busca_produto( codigo, volta_produto );
	}
	
	document.getElementById( 'CODIGOBARRA' ).value = codigo.trim( );
}

function volta_produto( cret ) 
{
    var objArr    = JSON.parse(cret);
    var descricao = '';
    var variante  = '';
    var controle  = '';
    
    controle  = objArr['produto_controle'    ] != '' ? objArr['produto_controle' ] : 0;
    variante  = objArr['variante_controle'   ] != '' ? objArr['variante_controle'] : 0;
    descricao = objArr['produto_nome'        ] != '' ? objArr['produto_nome'     ] : '';
    descricao = objArr['variante_nome'       ] != '' ? descricao + " " + objArr['variante_nome'] : descricao;
    
    document.getElementById('PRODUTO_CONTROLE' ).value = controle;
    document.getElementById('VARIANTE_CONTROLE').value = variante;
    document.getElementById('DESCRICAO_4'      ).value = descricao;
}


function focar_campo( valor )
{
	 if ( document.getElementById( 'LINHAFOCADA' ) ) 
       document.getElementById( 'LINHAFOCADA' ).value = valor;
   
   //if ( valor == true )
     if ( document.getElementById( 'UNIFOCO' ) )
       document.getElementById( 'UNIFOCO' ).value = 0;
	 
   //if ( document.getElementById( elemento ) )
   //{
	  // document.getElementById( elemento ).select();
   //}
}

function limparcampos( linha )
{
	document.getElementById( 'DESCRICAO_' + linha ).value = '';
}