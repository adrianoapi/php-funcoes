
function salvar_cadastro() {

var BAG              = document.getElementById('BAG'          ).value;
var EMAIL            = document.getElementById('EMAIL'        ).value;
var CPF              = document.getElementById('CPF'          ).value;
var NOME             = document.getElementById('NOME'         ).value;
var TELEFONE         = document.getElementById('TELEFONE'     ).value;
var LOGRADOURO       = document.getElementById('LOGRADOURO'   ).value;
var NUMERO           = document.getElementById('NUMERO'       ).value;
var CEP              = document.getElementById('CEP'          ).value;
var BAIRRO           = document.getElementById('BAIRRO'       ).value;
var CIDADE           = document.getElementById('CIDADE'       ).value;
var ESTADO           = document.getElementById('ESTADO'       ).value;
var COMPLEMENTO      = document.getElementById('COMPLEMENTO'  ).value;
var CODIGO_PEDIDO    = document.getElementById('CODIGO_PEDIDO').value;
var VALOR            = document.getElementById('VALOR'        ).value;
var TROCO            = document.getElementById('TROCO'        ).value;
var eFORMAPA_GAMENTO = document.getElementById('FORMAPA_GAMENTO');
var FORMAPA_GAMENTO  = eFORMAPA_GAMENTO.options[eFORMAPA_GAMENTO.selectedIndex].value;

x_salvar_cadastro(BAG, EMAIL, CPF, NOME, TELEFONE, LOGRADOURO, NUMERO, CEP, BAIRRO, CIDADE, ESTADO, COMPLEMENTO, FORMAPA_GAMENTO, CODIGO_PEDIDO, VALOR, TROCO, volta_cadastro);

}

function volta_cadastro(cret) {
    console.log(cret);
    var data = cret.split('||');
    if(data[0] == 'true'){
        alert('Dados enviados com sucesso. \n ID: ' + data[1]);
        parent.$.fancybox.close();
    }else{
        alert('Ocorreu um erro ao enviar os dados. \n ' + data[1]);
    }
}

function limparcampos( linha )
{
	document.getElementById( 'DESCRICAO_' + linha ).value = '';
}