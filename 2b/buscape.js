
function cor_linha1(tipo,linha) {
	if (tipo == '1') {
		document.getElementById('linha_2' + linha).bgColor = '#FFFF99';
	} else {
		document.getElementById('linha_2' + linha).bgColor = '#F2F2F2';
	}
}
function cor_linhad(tipo,linha) {
	if (tipo == '1') {
		document.getElementById('linha_d' + linha).bgColor = '#FFFF99';
	} else {
		document.getElementById('linha_d' + linha).bgColor = '#ECFFFF';
	}
}

function excluir_cadastro(controle) {
	if (confirm('Deseja cancelar o cadastro ?') == false) {
		return false;
	}
	x_excluir_cadastro(controle,volta_excluir_cadastro);
}

function volta_excluir_cadastro(cret){
	//history.go(0);
	location.reload();
	return false;
	}
	
function sincronizarMassa()
{
    var dados = [];
    $('#checkSincronizar :checked').each(function () {
        dados.push($(this).val());
    });
   
    if (dados.length < 1) {
        alert('Nenhum registro selecionado!');
        return false;
    }
    x_sincronizarMassa(dados, volta_exportacao_function);
}

function volta_exportacao_function(cret)
{
    var obj = JSON.parse(cret);
    var str = '';
    if(!obj['status']){
        str += obj['msg'] + '\n';
        if(obj['options'] instanceof Array){
            for(var i = 0; i < obj['options'].length; i++){
                str += '\n' + obj['options'][i]['titulo'] + '\n';
                for(var j = 0; j < obj['options'][i]['erro'].length; j++){
                    str += ' ' + obj['options'][i]['erro'][j] + '; \n';
                }
            }
        }
        alert(str);
    }else{
        alert('Solicitação realizada com sucesso!');
        location.reload();
	return false;
    }
}

function check_all(source) {
  checkboxes = document.getElementsByName('exportar[]');
  for( var i = 0, n = checkboxes.length; i < n; i++ ) {
    checkboxes[i].checked = source.checked;
  }
}