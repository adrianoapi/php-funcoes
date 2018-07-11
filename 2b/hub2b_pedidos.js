
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


	
function sincronizarMassa()
{

    var dados               = [];
    $('#checkSincronizar :checked').each(function () {
        dados.push($(this).val());
    });
   
    if (dados.length < 1) {
        alert('Nenhum registro selecionado!');
        return false;
    }
    
    document.getElementById('btn-sincronizar').value    = "Aguarde...";
    document.getElementById('btn-sincronizar').disabled = true;
    x_sincronizarMassa(dados, volta_exportacao_function);
}

function volta_exportacao_function(cret)
{
    console.log(cret);
    var obj = JSON.parse(cret);
    if(obj['status']){
        // SUCCESS
        alert('Sincronizado com sucesso!');
        location.reload();
    }else{
        // ERROR
        if(obj['msg'] != "" && obj['msg'] != null){
            var msg = obj['msg'] + '\n';
            
            console.log(obj);
            return false;
            
            if(obj['options']){
                // Erro na aplicacao
                // Loop titulo de erros
                for(var i =0; i < obj['options'].length; i++){
                    msg += '\n' + obj['options'][i]['titulo'];
                    // Loop de erros registrados
                    for(var j = 0; j < obj['options'][i]['erro'].length; j++){
                        msg += '\n -> ' + obj['options'][i]['erro'][j];
                    }
                    msg += '\n';
                }
            }else if(obj['error']){
                // Erro no WS
                // Exibe o erro informado pelo WS
                var array = obj['error']['error'].split(".");
                for(var k = 0; k < array.length; k++){
                    msg += '\n' + array[k];
                }
            }
            alert(msg);
        }else{
            alert('ERRO ao sincronizar!');
        }
    }
    document.getElementById('btn-sincronizar').value = "Sincronizar";
    document.getElementById('btn-sincronizar').disabled = false;
    return false;
}

function check_all(source) {
  checkboxes = document.getElementsByName('exportar[]');
  for( var i = 0, n = checkboxes.length; i < n; i++ ) {
    checkboxes[i].checked = source.checked;
  }
}