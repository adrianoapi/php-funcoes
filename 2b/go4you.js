
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
	
function apagarBag()
{
    var BAG = document.getElementById('idBag').value;
    if(BAG < 1){
        alert('Erro: Informe um ID para a BAG');
        return false;
    }

    x_apagarBag(BAG, volta_apagarbag_function);
}

function apagarBagPedido()
{
    var BAG = document.getElementById('idBagPedido').value;
    if(BAG < 1){
        alert('Erro: Informe um ID para a BAG');
        return false;
    }

    x_apagarBagPedido(BAG, volta_apagarbag_function);
}

function volta_apagarbag_function(cret)
{
    var data = cret.split('||');
    if(data[0] == 'true'){
        alert('Excluido com sucesso. \n ID: ' + data[1]);
        parent.$.fancybox.close();
    }else{
        alert('Ocorreu um erro ao excluir. \n ' + data[1]);
    }
}

function lerBag()
{
    var BAG = document.getElementById('idBag').value;
    if(BAG < 1){
        alert('Erro: Informe um ID para a BAG');
        return false;
    }

    x_lerBag(BAG, volta_lerbag_function);
}

function volta_lerbag_function(cret)
{
    alert(cret);
}

function check_all(source) {
  checkboxes = document.getElementsByName('exportar[]');
  for( var i = 0, n = checkboxes.length; i < n; i++ ) {
    checkboxes[i].checked = source.checked;
  }
}

online();
    
function online(time) {
    var time = time == "" ? 1 : time; 
    setTimeout(function () {
    // get integracao
    x_consultar_opedidos(volta_consultar_opedidos);
    x_data_hora(just_time);
    online(6000);
    }, time);
}

function update_status(pedido, status){
    
    var soma = parseInt(status) + 1;

    // Checa se é preciso mandar para o WS
    if(soma <= 2){
        x_button_update(pedido, soma, update_retorno);
    }else{
        x_update_status(pedido, soma, update_retorno);
    }
}

function update_retorno(){
    location.reload();
    return false;
}
/**
 * Desenha a tabela
 * @param {type} cret
 * @returns {undefined}
 */
function volta_consultar_opedidos(cret){
    buscar_erro();
    $("#lista-peidos").empty();
    
    var color_status = '';
    var obj = JSON.parse(cret);
    for(var i = 0; i < obj.length; i++){
        
        if(obj[i]['STATUS_GO4YOU_COD'] == 5){
            $("#lista-peidos").append(
                "<tr id=\"linha_2" + i + "\" bgcolor=\"#FF0000\"><td>" + obj[i]['CAIXACUPOM_CONTROLE'] +
                "</td><td>" + obj[i]['DATAHORA'] +
                "</td><td><div align=\"left\">" + obj[i]['TEMPO'] + "</div>" +
                "</td><td>" + obj[i]['STATUS_GO4YOU'] +
                "</td><td>" + obj[i]['STATUS_BTN'] +
                "</td></tr>");
        }else{
            $("#lista-peidos").append(
                "<tr id=\"linha_2" + i + "\" onMouseOut=\"cor_linha1('2','" + i + "')\" onMouseOver=\"cor_linha1('1','" + i + "')\" bgcolor=\"#F2F2F2\"><td>" + obj[i]['CAIXACUPOM_CONTROLE'] +
                "</td><td>" + obj[i]['DATAHORA'] +
                "</td><td><div align=\"left\">" + obj[i]['TEMPO'] + "</div>" +
                "</td><td>" + obj[i]['STATUS_GO4YOU'] +
                "</td><td>" + obj[i]['STATUS_BTN'] +
                "</td></tr>");
        }
    }
    
}

/*
 * Retorno data hora do servidor
 */
function just_time(cret){
    $('#just-time').html(cret);
}

/*
 * Busca os erros que ainda não foram exibidos ao usuário
 */
function buscar_erro(){
    var status = 0;
    x_buscar_erro(status, exibir_erro);
}

function exibir_erro(cret){
    $("#table-lista-erros").hide();
    $("#lista-erros").empty();
    if(cret != ""){
        $("#table-lista-erros").show();
        $("#lista-erros").append("<tr bgcolor=\"red\"><td><font color=\"#fff\">" + cret + "</font></td></tr>");
    }
    
}

function button_message(value){
    document.getElementById(value).disabled = true;
    document.getElementById(value).value    = "Aguarde...";
}