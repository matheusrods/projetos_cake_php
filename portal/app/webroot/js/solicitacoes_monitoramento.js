function init_combo_events(){
	jQuery("#RecebsmCodigoCliente").blur(function() {
			jQuery.ajax({
			"url": baseUrl + "clientes/clientes_monitora/" + jQuery(this).val() + "/" + Math.random(),
			"success": function(data) {
				jQuery("#RecebsmCodigoClienteMonitora").html(data);
			}
		});
	});
}

function init_combo_events_base_cnpj_por_tipo_empresa(tipo_empresa, input_target_id, input_source_id){
	if (input_source_id == null)
		input_source_id = "#RecebsmCodigoCliente, #RecebesmEmbarcadoresCodigoCliente, #RecebesmTransportadoresCodigoCliente";

	jQuery(input_source_id).blur(function(){
		jQuery(input_target_id).css("color","#000"); // change font-color to black
		jQuery(input_target_id+' option:selected').text('Aguarde, carregando...');
		jQuery.ajax({
			"url": baseUrl + "clientes/clientes_monitora_por_base_cnpj/" + jQuery(input_source_id).val() + "/" + tipo_empresa + "/" + Math.random(),
			"success": function(data) {
				jQuery(input_target_id).html(data).change();
				jQuery(input_target_id).css("color","#555555"); // return default font-color
			}
		});
	});
}

function init_combo_usuarios_cliente_monitora(input_target_id, input_source_id){
	jQuery(input_source_id).blur(function(){
		jQuery(input_target_id).css("color","#000"); // change font-color to black
		jQuery(input_target_id+' option:selected').text('Aguarde, carregando...');
		jQuery.ajax({
			"url": baseUrl + "usuarios/listar_clientes_monitora/" + jQuery(input_source_id).val() + "/" + Math.random(),
			"success": function(data) {
				jQuery(input_target_id).html(data).change();
				jQuery(input_target_id).css("color","#555555"); // return default font-color
			}
		});
	});
}

function init_input_cliente_monitora(input_target_id, input_source_id){
	jQuery(input_source_id).change(function(){
		jQuery.ajax({
			"url": baseUrl + "usuarios/usuario_monitora/" + jQuery(input_source_id).val() + "/" + Math.random(),
			"success": function(data) {
				jQuery(input_target_id).val(data);
				jQuery(input_target_id).blur();
			}
		});
	});
}

function transportadoras_por_embarcadores( input_target_id ) {
	jQuery("#RecebsmClienteEmbarcador").change(function () {
		var data_inicial = jQuery( '#RecebsmDataInicial' ).val();
		var data_final   = jQuery( '#RecebsmDataFinal' ).val();
		var cliente_embarcador = $(this).val();
		var data = [];
		if (cliente_embarcador != "") {
			data[0] = cliente_embarcador;
		} else {
			cliente_embarcador = $('#RecebsmClienteEmbarcador option');
			if (cliente_embarcador.length > 1) {
				for (var indice = 0; indice < cliente_embarcador.length; indice ++) {
					data[indice] = cliente_embarcador[indice].value;
				}
				data.shift();
			}
		}
		data = data.join();
		data = {'data_inicial':data_inicial, 'data_final':data_final, 'cliente_embarcador':data };
		jQuery.ajax({
			"type" : 'POST',
			"url" : baseUrl + "clientes/transportadoras_por_embarcadores/",
			"data" : data,
			"success" : function( dados ){
				jQuery(input_target_id).html(dados);
			}
		})
	});
}

function ponto_motorista(codigo_motorista, data_inicial, data_final, codigo_cliente, codigo_cliente_monitora) {
	var form = document.createElement("form");
	var form_id = ('formresult' + Math.random()).replace('.','');
	form.setAttribute("method", "post");
	form.setAttribute("target", form_id);
	form.setAttribute("action", "/portal/solicitacoes_monitoramento/ponto_motorista");
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][codigo_motorista]");
	field.setAttribute("value", codigo_motorista);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][data_inicial]");
	field.setAttribute("value", data_inicial);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][data_final]");
	field.setAttribute("value", data_final);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	document.body.appendChild(form);
	var janela = window_sizes();
	window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	form.submit();
}

function jornada_motorista(codigo_motorista, data_inicial, data_final, nova_jornada) {
	var form = document.createElement("form");
	var form_id = ('formresult' + Math.random()).replace('.','');
	form.setAttribute("method", "post");
	form.setAttribute("target", form_id);
	if(nova_jornada) {
		form.setAttribute("action", "/portal/viagens/listar_jornada_motorista");
	} else {
		form.setAttribute("action", "/portal/solicitacoes_monitoramento/jornada_motorista");
	}
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][codigo_motorista]");
	field.setAttribute("value", codigo_motorista);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][data_inicial]");
	field.setAttribute("value", data_inicial);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][data_final]");
	field.setAttribute("value", data_final);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	document.body.appendChild(form);
	var janela = window_sizes();
	window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	form.submit();
}
/*
function jornada_motorista_antiga(codigo_motorista, data_inicial, data_final, codigo_cliente, codigo_cliente_monitora) {
	var form = document.createElement("form");
	var form_id = ('formresult' + Math.random()).replace('.','');
	form.setAttribute("method", "post");
	form.setAttribute("target", form_id);
	form.setAttribute("action", "/portal/solicitacoes_monitoramento/jornada_motorista");
	//form.setAttribute("action", "/portal/viagens/listar_jornada_motorista");
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][codigo_motorista]");
	field.setAttribute("value", codigo_motorista);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][data_inicial]");
	field.setAttribute("value", data_inicial);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][data_final]");
	field.setAttribute("value", data_final);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	document.body.appendChild(form);
	var janela = window_sizes();
	window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	form.submit();
}

function jornada_motorista(codigo_motorista, data_inicial, data_final, codigo_cliente, codigo_cliente_monitora) {
	var form = document.createElement("form");
	var form_id = ('formresult' + Math.random()).replace('.','');
	form.setAttribute("method", "post");
	form.setAttribute("target", form_id);
	//form.setAttribute("action", "/portal/solicitacoes_monitoramento/jornada_motorista");
	form.setAttribute("action", "/portal/viagens/listar_jornada_motorista");
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][codigo_motorista]");
	field.setAttribute("value", codigo_motorista);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][data_inicial]");
	field.setAttribute("value", data_inicial);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	field = document.createElement("input");
	field.setAttribute("name", "data[Recebsm][data_final]");
	field.setAttribute("value", data_final);
	field.setAttribute("type", "hidden");
	form.appendChild(field);
	document.body.appendChild(form);
	var janela = window_sizes();
	window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	form.submit();
}
*/
function atualizaListaTransitTime() {
	var div = jQuery("div.lista");
	bloquearDiv(div);
	div.load(baseUrl + "/solicitacoes_monitoramento/transit_time_listagem/" + Math.random());
}

function transitTimeListagem(p_max_items, p_interval) {
	bloquearDiv(jQuery('.lista'));
	jQuery.ajax({
		"type": 'GET',
		"dataType": 'JSON',
		"url": baseUrl + "solicitacoes_monitoramento/transit_time_dados/" + Math.random(),
		"success" : function( dados ){
			if ( dados != null && dados != false ) {
				data = dados.sort(function(a,b){ return parseInt(b.status) - parseInt(a.status)});
				max_items = p_max_items;
				interval = p_interval;
				proxima_leitura = new Date();
				proxima_leitura.setTime(proxima_leitura.getTime() + (5*60*1000));
			} else {
				data = [];
			}
			transitTimeApresentacao();
		},
		"error" : function() {
			transitTimeApresentacao();
		}
	})
}
var transitTimeHandler, i, last_item, data, max_items, interval, proxima_leitura;
function transitTimeApresentacao() {
	i = 0;
	if (typeof(data) == 'undefined') {
		data = new Array();
	}
	last_item = Math.min(data.length, (i + max_items)) -1;
	var normal = 0;
	var atrasado = 0;
	var muito_atrasado = 0;
	var sem_posicionamento = 0;
	for (var item = 0; item < data.length; item++) {
		if (data[item].status == 0) normal++;
		if (data[item].status == 1) atrasado++;
		if (data[item].status == 2) muito_atrasado++;
		if (data[item].TUposUltimaPosicao.upos_descricao_sistema == null) sem_posicionamento++;
	}
	jQuery('span#qtd-viagens').html( data.length );
	jQuery('span#viagens-normal').html( '<span class="text-success">' + normal + '</span>' );
	jQuery('span#viagens-atrasado').html( '<span class="text-warning">' + atrasado + '</span>' );
	jQuery('span#viagens-muito-atrasado').html( '<span class="text-error">' + muito_atrasado + '</span>' );
	jQuery('span#viagens-sem-posicionamento').html( '<span class="text-error">' + sem_posicionamento + '</span>' );
	window.clearInterval(transitTimeHandler);
	transitTimeEscreveLista();
	transitTimeHandler = window.setInterval("transitTimeEscreveLista()" ,interval*1000)
	jQuery('.lista').unblock();
}

function transitTimeNextPage() {
	transitTimeEscreveLista();
}

function transitTimePreviousPage() {
	i = i - (max_items * 2);
	if (i < 0)
		i = 0;
	last_item = Math.min(data.length, (i + max_items)) -1;
	transitTimeEscreveLista();
}

function setupLimparTransitTime() {
	jQuery("#limpar-filtro").click(function(){
		window.clearInterval(transitTimeHandler);
		bloquearDiv(jQuery(".form-procurar"));
		jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TransitTime/element_name:transit_time/" + Math.random())
	});
}

function transitTimeEscreveLista() {
	var data_inicio_real;
	var data_final_real;
	var posicao_atual_descricao;
	var posicao_destino_descricao;
	var posicao_origem_descricao;
	var striped_color;
	var badge_status;
	var title_status;
	var badge_em_movimento;
	var title_em_movimento;
	var linhas;
	if (i > last_item) {
		i = 0;
		last_item = Math.min(data.length, (i + max_items)) -1 ;
		var atual = new Date();
	   if (proxima_leitura.getTime() <= atual.getTime()) {
			transitTimeListagem(max_items, interval);
	   }

	}
	linhas = '';
	for (i; i <= last_item; i++) {
		if (data[i].TVeicVeiculo.veic_placa == null || data[i].TVeicVeiculo.veic_placa == false)
			data[i].TVeicVeiculo.veic_placa = '';
		data_inicio_real = data[i].TViagViagem.viag_data_inicio;
		data_final_real = data[i].TViagViagem.viag_data_fim;
		posicao_atual_descricao = (data[i].TUposUltimaPosicao.upos_descricao_sistema != null ? data[i].TUposUltimaPosicao.upos_descricao_sistema : '');
		posicao_destino_descricao = (data[i].Destino.refe_descricao != null ? data[i].Destino.refe_descricao : '');
		posicao_origem_descricao = (data[i].Origem.refe_descricao != null ? data[i].Origem.refe_descricao : '');
		if (data_inicio_real == null || data_inicio_real == false)
			data_inicio_real = '';
		if (data_final_real == null || data_final_real == false)
			data_final_real = $.datepicker.formatDate('dd/mm/yy', new Date());
		striped_color = (i%2 == 0 ? '' : 'background-color:#EEEEEE');
		badge_status_position = (posicao_atual_descricao == '' || posicao_destino_descricao == '') ? '' : 'badge-success';
		title_status_position = (posicao_atual_descricao == '' || posicao_destino_descricao == '') ? 'Sem Posicionamento' : 'Posicionamento Normal';
		badge_status = (data[i].status == 0 ? 'badge-success' : (data[i].status == 1 ? 'badge-warning' : 'badge-important'));
		title_status = (data[i].status == 0 ? 'Normal' : (data[i].status == 1 ? 'Atrasado' : 'Muito Atrasado'));
		badge_em_movimento = (data[i].em_movimento == 'S') ? 'badge-success' : 'badge-important';
		title_em_movimento = (data[i].em_movimento == 'S') ? 'Em movimento' : 'Sem movimento';

		var listaLoadPlan = '';
		var listaNF = '';
		if (data[i].VLocalViagem.length > 0) {
			for (var iLoad = 0; iLoad < data[i].VLocalViagem.length; iLoad++) {
				if (data[i].VLocalViagem[0].vnfi_pedido != undefined) {
					listaLoadPlan += data[i].VLocalViagem[iLoad].vnfi_pedido + ',';
				}
				if (data[i].VLocalViagem[0].vnfi_numero != undefined) {
					listaNF += data[i].VLocalViagem[iLoad].vnfi_numero + ',';
				}
			}
			listaLoadPlan = listaLoadPlan.substr(0, listaLoadPlan.length -1);
			listaNF = listaNF.substr(0, listaNF.length -1);
		}

		var regiao_alvo = (data[i][0].regiao_primeiro_alvo != undefined ? data[i][0].regiao_primeiro_alvo : '');

		linhas += '<tr style="' + striped_color + '">';
		linhas += '<td>' + (i + 1) + '</td>';
		if (/[a-z]/i.test(data[i].TVeicVeiculo.veic_placa[0]))
		  linhas += '<td>' + helper_placa(data[i].TVeicVeiculo.veic_placa, data_inicio_real, data_final_real) + '</td>';
		else
		  linhas += '<td>' + data[i].TVeicVeiculo.veic_chassi + '</td>';

		if(posicao_origem_descricao.length > 15)
			texto = posicao_origem_descricao.substr(0,15)+'..';
		else
			texto = posicao_origem_descricao.substr(0,15);
		linhas += '<td class="medium" style="width: 125px;"><span class="resumo"  data-toggle="tooltip"  title="'+posicao_origem_descricao+'">' + helper_itinerario(texto, data[i].TViagViagem.viag_codigo_sm) + '</span></td>';
		if(posicao_destino_descricao.length > 15)
			texto = posicao_destino_descricao.substr(0,15)+'..';
		else
			texto = posicao_destino_descricao.substr(0,15);
		linhas += '<td class="medium" style="width: 125px;"><span class="resumo"  data-toggle="tooltip"  title="'+posicao_destino_descricao+'">' + helper_itinerario(texto, data[i].TViagViagem.viag_codigo_sm) + '</span></td>';
		if(regiao_alvo.length > 15)
			texto = regiao_alvo.substr(0,15)+'..';
		else
			texto = regiao_alvo.substr(0,15);
		linhas += '<td><span class="resumo"  data-toggle="tooltip"  title="'+regiao_alvo+'">' + texto +'</span></td>';
		linhas += '<td>' + data[i].TViagViagem.viag_previsao_inicio +'</td>';
		linhas += '<td>' + data[i].TViagViagem.viag_previsao_fim + '</td>';
		linhas += '<td>' + data_inicio_real + '</td>';
		//linhas += '<td><span class="badge-empty badge ' + badge_status_position + '" title="' + title_status_position + '"><i class="icon-map-marker"></i></span>';
		linhas += '<td><span class="badge-empty badge ' + badge_status_position + '" title="' + title_status_position + '"><i class="icon-map-marker"></i></span>';
		//linhas += '<span class="badge-empty badge ' + badge_status + '" title="' + title_status + '"></span>';
		linhas += '<span class="badge-empty badge ' + badge_status + '" title="' + title_status + '"><i class="icon-time"></i></span>';
		//linhas += '<span class="badge-empty badge ' + badge_em_movimento + '" title="' + title_em_movimento + '"></span>';
		linhas += '<span class="badge-empty badge ' + badge_em_movimento + '" title="' + title_em_movimento + '"><i class="icon-road"></i></span>';		
		linhas += '</td>';
		linhas += '</tr>';
		linhas += '<tr style="' + striped_color + '">';
		linhas += '<td></td>';
		linhas += '<td>' + helper_codigo_sm(data[i].TViagViagem.viag_codigo_sm) + '</td>';
		linhas += '<td>' + helper_posicao_geografica(data[i].TUposUltimaPosicao.upos_latitude, data[i].TUposUltimaPosicao.upos_longitude, posicao_atual_descricao.substr(0,38), data[i].TVeicVeiculo.veic_placa) + '</td>';
		linhas += '<td class="numeric"><strong>Restante:</strong></td>';
		linhas += '<td>' + (data[i].distancia > 0 ? data[i].distancia + ' Km' : '') + '</td>';
		linhas += '<td>' + (data[i].tempo != '0' ? data[i].tempo_em_minutos : '') + '</td>';
		linhas += '<td>' + (data[i].VLocalViagem[0] == undefined ? '' : '<span title="' + listaLoadPlan + '">' + (data[i].VLocalViagem[0].vnfi_pedido == undefined ? '' : data[i].VLocalViagem[0].vnfi_pedido) + '</span>') + '</td>';
		linhas += '<td>' + (data[i].VLocalViagem[0] == undefined ? '' : '<span title="' + listaNF + '"><strong>NF:</strong>' + (data[i].VLocalViagem[0].vnfi_numero == undefined ? '' : data[i].VLocalViagem[0].vnfi_numero) + '</span>') + '</td>';
		linhas += '</tr>';
		linhas += '<tr style="' + striped_color + '">';
		linhas += '<td colspan="3"></td>';
		linhas += '<td><strong>Previsto: </strong>'+data[i].tempo_previsto+'</td>';
		linhas += '<td><strong>Total KM: </strong>'+(data[i].TViagViagem.viag_distancia?data[i].TViagViagem.viag_distancia:0)+'</td>';
		linhas += '<td><strong>Total Viag: </strong>'+(data[i].tempo_rodado_total?data[i].tempo_rodado_total:0)+'</td>';
		linhas += '<td><strong>Alvos: </strong>'+data[i].tempo_alvos+'</td>';
		linhas += '<td><strong>Rodado: </strong>'+ (data[i].tempo_rodado_desc?data[i].tempo_rodado_desc:0)+'</td>';
		linhas += '<td></td>';
		linhas += '</tr>';
	}
	i = last_item + 1;
	last_item = Math.min(data.length, (i + max_items)) -1;
	jQuery('tbody#transit-time').html(linhas);
}

function defineStatus(data_prevista){
	return 'badge-success';
}

function listar_acompanhamento_sms_embarcador(Model, codigo_embarcador, cliente_embarcador, codigo_transportador, cliente_transportador, data_inicial, data_final, sm_encerrada){
	form  = '<div id="postlink" style="display:none;">';
	form += '<form accept-charset="utf-8" method="post" id="'+Model+'ListarAcompSMs" action="/portal/solicitacoes_monitoramento/consulta_geral">'
	form += '<input type="text" id="'+Model+'Sm" value="" name="data['+Model+'][sm]">';
	form += '<input type="text" id="'+Model+'CodOperador" value="" name="data['+Model+'][cod_operador]">';
	form += '<input type="text" id="'+Model+'Operador" value="" name="data['+Model+'][operador]">';
	form += '<input type="text" id="'+Model+'DescricaoCidade" value="" name="data['+Model+'][descricao_cidade]">';
	form += '<input type="text" id="'+Model+'ValSmDe" value="" name="data['+Model+'][ValSmDe]">';
	form += '<input type="text" id="'+Model+'ValSmAte" value="" name="data['+Model+'][ValSmAte]">';
	form += '<input type="text" id="'+Model+'Codequipamento" value="" name="data['+Model+'][codequipamento]">';
	form += '<input type="text" id="'+Model+'CodOperacao" value="" name="data['+Model+'][cod_operacao]">';
	form += '<input type="text" id="'+Model+'CodigoEmbarcador" value="" name="data['+Model+'][codigo_embarcador]">';
	form += '<input type="text" id="'+Model+'ClienteEmbarcador" value="" name="data['+Model+'][cliente_embarcador]">';
	form += '<input type="text" id="'+Model+'CodigoTransportador" value="" name="data['+Model+'][codigo_transportador]">';
	form += '<input type="text" id="'+Model+'ClienteTransportador" value="" name="data['+Model+'][cliente_transportador]">';
	form += '<input type="text" id="'+Model+'DataInicial" value="" name="data['+Model+'][data_inicial]">';
	form += '<input type="text" id="'+Model+'DataFinal" value="" name="data['+Model+'][data_final]">';
	form += '<input type="checkbox" id="'+Model+'Status" value="" name="data['+Model+'][status]">';
	form += '<input type="checkbox" id="'+Model+'Status3" checked="checked" value="3" name="data['+Model+'][status][]">';
	form += '<input type="text" id="'+Model+'CodigoCidade" value="" name="data['+Model+'][codigo_cidade]">';
	form += '</form>';
	form += '</div>';

	jQuery('body').append(form);

	jQuery("#postlink #"+Model+"CodigoEmbarcador").val(codigo_embarcador);
	jQuery("#postlink #"+Model+"ClienteEmbarcador").val(cliente_embarcador);
	jQuery("#postlink #"+Model+"CodigoTransportador").val(codigo_transportador);
	jQuery("#postlink #"+Model+"ClienteTransportador").val(cliente_transportador);
	jQuery("#postlink #"+Model+"DataInicial").val(data_inicial);
	jQuery("#postlink #"+Model+"DataFinal").val(data_final);
	jQuery("#postlink #"+Model+'Sm').val('');
	jQuery("#postlink #"+Model+'CodOperador').val('');
	jQuery("#postlink #"+Model+'Operador').val('');
	jQuery("#postlink #"+Model+'DescricaoCidade').val('');
	jQuery("#postlink #"+Model+'ValSmDe').val('');
	jQuery("#postlink #"+Model+'ValSmAte').val('');
	jQuery("#postlink #"+Model+'Codequipamento').val('');
	jQuery("#postlink #"+Model+'CodOperacao').val('');
	jQuery("#postlink #"+Model+"Status").val('');
	jQuery("#postlink #"+Model+"Status3").val(sm_encerrada);
	jQuery("#postlink #"+Model+"CodigoCidade").val('');

	jQuery("#postlink form").submit();
}

function listar_acompanhamento_sms(Model,codigo_embarcador,cliente_embarcador,codigo_cliente,cliente_tipo,data_inicial,data_final,sm_encerrada,codigo_cidade){
	form  = '<div id="postlink" style="display:none;">';
	form += '<form accept-charset="utf-8" method="post" id="'+Model+'ListarAcompSMs" action="/portal/solicitacoes_monitoramento/consulta_geral">'
	form += '<input type="text" id="'+Model+'Sm" value="" name="data['+Model+'][sm]">';
	form += '<input type="text" id="'+Model+'CodOperador" value="" name="data['+Model+'][cod_operador]">';
	form += '<input type="text" id="'+Model+'Operador" value="" name="data['+Model+'][operador]">';
	form += '<input type="text" id="'+Model+'DescricaoCidade" value="" name="data['+Model+'][descricao_cidade]">';
	form += '<input type="text" id="'+Model+'ValSmDe" value="" name="data['+Model+'][ValSmDe]">';
	form += '<input type="text" id="'+Model+'ValSmAte" value="" name="data['+Model+'][ValSmAte]">';
	form += '<input type="text" id="'+Model+'Codequipamento" value="" name="data['+Model+'][codequipamento]">';
	form += '<input type="text" id="'+Model+'CodOperacao" value="" name="data['+Model+'][cod_operacao]">';
	form += '<input type="text" id="'+Model+'CodigoEmbarcador" value="" name="data['+Model+'][codigo_embarcador]">';
	form += '<input type="text" id="'+Model+'ClienteEmbarcador" value="" name="data['+Model+'][cliente_embarcador]">';
	form += '<input type="text" id="'+Model+'CodigoTransportador" value="" name="data['+Model+'][codigo_transportador]">';
	form += '<input type="text" id="'+Model+'ClienteTransportador" value="" name="data['+Model+'][cliente_transportador]">';
	form += '<input type="text" id="'+Model+'DataInicial" value="" name="data['+Model+'][data_inicial]">';
	form += '<input type="text" id="'+Model+'DataFinal" value="" name="data['+Model+'][data_final]">';
	form += '<input type="checkbox" id="'+Model+'Status" value="" name="data['+Model+'][status]">';
	form += '<input type="checkbox" id="'+Model+'Status3" checked="checked" value="3" name="data['+Model+'][status][]">';
	form += '<input type="text" id="'+Model+'CodigoCidade" value="" name="data['+Model+'][codigo_cidade]">';
	form += '</form>';
	form += '</div>';

	jQuery('body').append(form);

	jQuery("#postlink #"+Model+"CodigoEmbarcador").val(codigo_embarcador);
	jQuery("#postlink #"+Model+"ClienteEmbarcador").val(cliente_embarcador);
	jQuery("#postlink #"+Model+"CodigoTransportador").val(codigo_cliente);
	jQuery("#postlink #"+Model+"ClienteTransportador").val(cliente_tipo);
	jQuery("#postlink #"+Model+"DataInicial").val(data_inicial);
	jQuery("#postlink #"+Model+"DataFinal").val(data_final);
	jQuery("#postlink #"+Model+'Sm').val('');
	jQuery("#postlink #"+Model+'CodOperador').val('');
	jQuery("#postlink #"+Model+'Operador').val('');
	jQuery("#postlink #"+Model+'DescricaoCidade').val('');
	jQuery("#postlink #"+Model+'ValSmDe').val('');
	jQuery("#postlink #"+Model+'ValSmAte').val('');
	jQuery("#postlink #"+Model+'Codequipamento').val('');
	jQuery("#postlink #"+Model+'CodOperacao').val('');
	jQuery("#postlink #"+Model+"Status").val('');
	jQuery("#postlink #"+Model+"Status3").val(sm_encerrada);
	jQuery("#postlink #"+Model+"CodigoCidade").val(codigo_cidade);

	jQuery("#postlink form").submit();
}

function consulta_geral(cliente_embarcador, cliente_transportador, data_inicial, data_final, status){
	var form = document.createElement("form");
	var form_id = ('formresult' + Math.random()).replace('.','');
	form.setAttribute("method", "post");
	form.setAttribute("action", "/portal/solicitacoes_monitoramento/consulta_geral");
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][sm]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][cod_operador]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][operador]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][data_inicial]");field.setAttribute("value", data_inicial);field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][data_final]");field.setAttribute("value", data_final);field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][codigo_embarcador]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][cliente_embarcador]");field.setAttribute("value", cliente_embarcador);field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][codigo_transportador]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][cliente_transportador]");field.setAttribute("value", cliente_transportador);field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][tipo_estatistica]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][ValSmDe]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][ValSmAte]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][descricao_cidade]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][ValSmDe]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][ValSmAte]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][status][]");field.setAttribute("value", status);field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][codequipamento]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][tipo_filtro_operacoes]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	field = document.createElement("input");field.setAttribute("name", "data[Recebsm][cod_operacao]");field.setAttribute("value", '');field.setAttribute("type", "hidden");form.appendChild(field);
	document.body.appendChild(form);
	form.submit();
}

function listar_acompanhamento_sms_por_origem_destino(Model,codigo_cliente,data_inicial,data_final,cod_cidade,nome_cidade,tipo_estatistica,sm_encerrada){
	//capt = tipo_do_cliente;
	form  = '<div id="postlink" style="display:none;">';
	form += '<form accept-charset="utf-8" method="post" id="'+Model+'ListarAcompSMs" action="/portal/solicitacoes_monitoramento/pre_filtro_consulta_geral_estatistica">'
	form += '<input type="text" id="'+Model+'CodigoCliente" value="" name="data['+Model+'][codigo_cliente]">';
	form += '<input type="text" id="'+Model+'DataInicial" value="" name="data['+Model+'][data_inicial]">';
	form += '<input type="text" id="'+Model+'DataFinal" value="" name="data['+Model+'][data_final]">';
	form += '<input type="text" id="'+Model+'CodigoCidade" value="" name="data['+Model+'][codigo_cidade]">';
	form += '<input type="text" id="'+Model+'DescricaoCidade" value="" name="data['+Model+'][descricao_cidade]">';
	form += '<input type="text" id="'+Model+'TipoEstatistica" value="" name="data['+Model+'][tipo_estatistica]">';
	form += '<input type="checkbox" id="'+Model+'Status" value="" name="data['+Model+'][status]">';
	form += '<input type="checkbox" id="'+Model+'Status7" checked="checked" value="" name="data['+Model+'][status][]">';
	form += '</form>';
	form += '</div>';

	jQuery('body').append(form);
	jQuery("#postlink #"+Model+"Codigo").val(codigo_cliente);
	jQuery("#postlink #"+Model+"DataInicial").val(data_inicial);
	jQuery("#postlink #"+Model+"DataFinal").val(data_final);
	jQuery("#postlink #"+Model+"CodigoCidade").val(cod_cidade);
	jQuery("#postlink #"+Model+"DescricaoCidade").val(nome_cidade);
	jQuery("#postlink #"+Model+"TipoEstatistica").val(tipo_estatistica);
	jQuery("#postlink #"+Model+"Status").val('');
	jQuery("#postlink #"+Model+"Status7").val(sm_encerrada);
	jQuery("#postlink form").submit();
}

var setaIntervalo;
var codigo_evento;

function carregaDadosSituacaoMonitoramento( qdtEventos, intervalo, telao ){

	 $.ajaxSetup({ cache: false });

	 $.ajax({

		type: 'POST',
		dataType: 'JSON',
		url: '/portal/solicitacoes_monitoramento/situacao_monitoramento_grafico/' + Math.random(),
		cache:false,
		data:{
			'intervalo':intervalo
		},

		beforeSend : function(){
			window.clearInterval(setaIntervalo);
			bloquearDiv(jQuery('.lista'));
		},

		success : function(data) {

			codigo_evento	= data.codigo_evento;
			var eventos   = data.dadosGrafico.eixo_x;
			var dentro_do_sla	= data.dadosGrafico.series[0].values;
			var fora_do_sla  = data.dadosGrafico.series[1].values;
			var em_viagem	  = data.dadosSm.em_viagem;
			var finalizadas_no_dia = data.dadosSm.finalizadas_no_dia;
			var iniciadas_no_dia   = data.dadosSm.iniciadas_no_dia;
			var intervalo	  = data.intervalo;

			if( telao != undefined ){
				escreveDadosSM(em_viagem, finalizadas_no_dia, iniciadas_no_dia, data.dadosSm.paradas);
				$('#info-sm').css( 'visibility', 'visible' );
			} else {
				escreveDadosEventos(eventos, dentro_do_sla, fora_do_sla);
				$('#info-eventos').css( 'visibility', 'visible' );
			}
			
			carregaDadosSMSemOperador(1);

			paginacaoGrafico( eventos, dentro_do_sla, fora_do_sla, qdtEventos, intervalo );
			jQuery('.lista').unblock();

		},

		error : function() {
			carregaDadosSituacaoMonitoramento( qdtEventos, intervalo, telao );
		}

	 });

}


function escreveDadosSM( em_viagem, finalizadas_no_dia, iniciadas_no_dia, paradas ) {

	$('#viagem').html( "<center>"+formata_numeros(em_viagem)+"</center>" );
	$('#paradas').html( "<center>"+formata_numeros(paradas,0)+"</center>" );
	$('#iniciadas').html( "<center>"+formata_numeros(iniciadas_no_dia,0)+"</center>" );
	$('#finalizadas').html( "<center>"+formata_numeros(finalizadas_no_dia,0)+"</center>" );
}


function escreveDadosEventos( eventos, dentro_do_sla, fora_do_sla ) {

	var html;
	var total_fora = 0;
	var total_dentro = 0;
	var total_dentro_fora = 0;

	for( var i = 0; i < eventos.length; i++ ) {

		html += '<tr>';
			html += '<td>'+eventos[i]+'</td>';
			html += '<td class="numeric"><a href="javascript:void(0)" onclick="situacao_monitoramento_detalhes_evento_viagem('+codigo_evento[i]+', 1)">'+dentro_do_sla[i]+'</a></td>';
			html += '<td class="numeric"><a href="javascript:void(0)" onclick="situacao_monitoramento_detalhes_evento_viagem('+codigo_evento[i]+', 0)">'+fora_do_sla[i]+'</a></td>';
			html += '<td class="numeric">'+(dentro_do_sla[i]+fora_do_sla[i])+'</td>';
		html += '</tr>';

		total_dentro += dentro_do_sla[i];
		total_fora   += fora_do_sla[i];
	}

	total_dentro_fora = total_dentro + total_fora;

	$('#dados-eventos').html(html);
	$('#total-dentro').html(formata_numeros(total_dentro));
	$('#total-fora').html(formata_numeros(total_fora));
	$('#total-dentro-fora').html(formata_numeros(total_dentro_fora));
}


function paginacaoGrafico( eventos, dentro_do_sla, fora_do_sla, qdtEventos, intervalo ) {

   var eventos   = eventos;
   var dentro_do_sla = dentro_do_sla;
   var fora_do_sla   = fora_do_sla;
   var valorIni   = 0;
   var valorFim   = 0;
   var valorFinal   = 0;
   var quantidade   = 0;
   var qdtDados   = 0;
   var pagina	 = 1;
   var tempo;
   var pg;

   tempo	  = ( intervalo < 5 ) ? 5 : intervalo;
   tempo	  = tempo * 1000
   quantidade = ( qdtEventos > 5 ) ? 5 : qdtEventos;

   qdtDados   = eventos.length;
   valorFinal = ( qdtDados <= quantidade ) ? qdtDados : quantidade;
   valorFim   = valorFinal;
   pg	  = ( qdtDados > quantidade ) ? Math.ceil( qdtDados/quantidade ) : '1';
   $('#info-pagina-dtr').html( '1/'+pg );
   escreveDadosGrafico( eventos, dentro_do_sla, fora_do_sla, valorIni, valorFim );

   setaIntervalo = window.setInterval(
		function(){
			if( qdtDados > valorFim ){
				pagina++;
				valorIni = valorFim;
				valorFim = ( ( pagina * quantidade ) >= qdtDados ) ? qdtDados : ( pagina * quantidade );
				escreveDadosGrafico( eventos, dentro_do_sla, fora_do_sla, valorIni, valorFim );
				$('#info-pagina-dtr').html( pagina+'/'+pg );
			} else {
				window.clearInterval(setaIntervalo);
				carregaDadosSituacaoMonitoramento(qdtEventos, intervalo);
			}
		},
		tempo
	);

}


function escreveDadosGrafico( eventos, dentro_do_sla, fora_do_sla, valorIni, valorFim ){

	$('#grafico').css( 'display', 'block' );
	$('#info-pagina').css( 'visibility', 'visible' );

	var _eventos	   = [];
	var _dentro_do_sla = [];
	var _fora_do_sla   = [];
	var n = 0;

	for( var i = valorIni; i < valorFim; i++ ) {

		_eventos[n] = eventos[i];
		_dentro_do_sla[n] = dentro_do_sla[i];
		_fora_do_sla[n]   = fora_do_sla[i];

		n++;
	}

	n = 0;

	chart.xAxis[0].setCategories( _eventos );
	chart.series[0].setData( _dentro_do_sla );
	chart.series[1].setData( _fora_do_sla );

	_eventos	   = [];
	_dentro_do_sla = [];
	_fora_do_sla   = [];
}


function telao_buonnysat( qdtEventos, intervalo ){

	var newwindow = window.open('/portal/solicitacoes_monitoramento/situacao_monitoramento_telao/'+qdtEventos+'/'+intervalo,
		'_blank', 'top=0,left=0,width='+screen.width+',height='+screen.height);
	if (window.focus){
		newwindow.focus()
	}
}

function telao_cockpit_buonnysat(){

	var newwindow = window.open('/portal/solicitacoes_monitoramento/cockpit_sm_telao',
		'_blank', 'top=0,left=0,width='+screen.width+',height='+screen.height);
	if (window.focus){
		newwindow.focus()
	}
}

function situacao_monitoramento_detalhes_evento_viagem( codigo_evento, status ){

	form  = '<div id="postlink" style="display:none;">';
	form += '<form accept-charset="utf-8" method="post" id="RecebsmSituacaoMonitoramentoDetalhesEventoViagem" action="/portal/solicitacoes_monitoramento/situacao_monitoramento_detalhes_evento_viagem">'
	form += '<input type="text" id="TEspaEventoSistemaPadraoEspaCodigo" value="" name="data[TEspaEventoSistemaPadrao][espa_codigo]">';
	form += '<input type="text" id="TEspaEventoSistemaPadraoEspaSla" value=""  name="data[TEspaEventoSistemaPadrao][espa_sla]">';
	form += '</form>';
	form += '</div>';

	jQuery('body').append(form);
	jQuery("#postlink #TEspaEventoSistemaPadraoEspaCodigo").val(codigo_evento);
	jQuery("#postlink #TEspaEventoSistemaPadraoEspaSla").val(status);

	jQuery("#postlink form").submit();
}

var telaoBuonnySatSMsEncerradas;

function carregaDadosTelaoBuonnySatSMsEncerradas() {

	$(function(){

		$.ajaxSetup({ cache: false });

		 $.ajax({

			type: 'POST',
			dataType: 'HTML',
			url: '/portal/solicitacoes_monitoramento/cockpit_sm/' + Math.random(),
			cache:false,

			beforeSend : function(){
				window.clearInterval(telaoBuonnySatSMsEncerradas);
				$('.lista').html('');
				bloquearDiv(jQuery('.lista'));
			},

			success : function(data) {
				$('.lista').html(data);
				$('#nagevacao').css('display', 'none');
				jQuery('.lista').unblock();

				atualizaDadosTelaoBuonnySatSMsEncerradas();
			},

			error : function() {
				carregaDadosTelaoBuonnySatSMsEncerradas();
			}

		});

	})
}

function atualizaDadosTelaoBuonnySatSMsEncerradas(){

	telaoBuonnySatSMsEncerradas = window.setInterval(
		function(){
			carregaDadosTelaoBuonnySatSMsEncerradas();
		},
		300000
	);
}

var setaIntervaloTemperatura, valorIniTemperatura, valorFimTemperatura, qtdDadosTemperatura,
	intervaloTemperatura, quantidadeTemperatura, dadosTemperatura, paginaTemperatura, pg, id_temp=0;


function _carregaDadosAcompanhamentoTemperatura( data_obj ) {
	data_obj = merge({
		qtd: 		0,
		intervalo: 	0,
		cliente: 	null,
		tipo: 		null,
		retorno: 	null
	},data_obj || {});

	$.ajax({

		type: 'POST',
		url: '/portal/viagens/dados_acompanhamento_temperatura/' + Math.random(),
		data:{
			"data[Recebsm][codigo_cliente]" : data_obj.cliente,
			"data[Recebsm][cliente_tipo]"   : data_obj.tipo
		},

		beforeSend : function(){
			window.clearInterval(setaIntervaloTemperatura);
			bloquearDiv(jQuery(data_obj.retorno));
		},

		success : function(data){

			jQuery(data_obj.retorno).html(data);
			intervaloTemperatura  = data_obj.intervalo;
			quantidadeTemperatura = data_obj.qtd;

			jQuery(data_obj.retorno).unblock();
		},

		error : function(){

		}
	})

}

function merge() {
    var obj, name, copy,
        target = arguments[0] || {},
        i = 1,
        length = arguments.length;

    for (; i < length; i++) {
        if ((obj = arguments[i]) != null) {
            for (name in obj) {
                copy = obj[name];

                if (target === copy) {
                    continue;
                }
                else if (copy !== undefined) {
                    target[name] = copy;
                }
            }
        }
    }

    return target;
};

function carregaDadosAcompanhamentoTemperatura( quantidade, intervalo, codigo_cliente, cliente_tipo ) {

	$(function(){

		$.ajaxSetup({ cache : false });

		$.ajax({

			type: 'POST',
			dataType: 'JSON',
			url: '/portal/viagens/dados_acompanhamento_temperatura',
			cache: false,
			data:{
				"data[Recebsm][codigo_cliente]" : codigo_cliente,
				"data[Recebsm][cliente_tipo]"   : cliente_tipo
			},

			beforeSend : function(){
				window.clearInterval(setaIntervaloTemperatura);
				bloquearDiv(jQuery('.lista'));
			},

			success : function(data){

				qtdDadosTemperatura   = data.length;
				dadosTemperatura	  = data;
				intervaloTemperatura  = intervalo;
				quantidadeTemperatura = quantidade;
				paginacaoDadosAcompanhamentoTemperatura( codigo_cliente, cliente_tipo );

				$('#info-cliente').css('display', 'block');
				$('#cliente-codigo').html(data[0]['codigo']);
				$('#cliente-razao-social').html(data[0]['razao_social']);
				$('#qtd-viagens').html(data[0]['qtd_viagens']);
				$('#posicionamento-normal').html(data[0]['qtd_normal']);
				$('#sem-posicionamento').html( data[0]['qtd_viagens'] - data[0]['qtd_normal'] );

				jQuery('.lista').unblock();
			},

			error : function(){

			}
		})
	})
}


function paginacaoDadosAcompanhamentoTemperatura( codigo_cliente, cliente_tipo) {

   valorIniTemperatura   = 0;
   valorFimTemperatura   = 0;
   paginaTemperatura	 = 1;
   var tempo;

   tempo	  = ( intervaloTemperatura < 5 ) ? 5 : intervaloTemperatura;
   tempo	  = tempo * 1000;

   valorFimTemperatura   = ( qtdDadosTemperatura <= quantidadeTemperatura ) ? qtdDadosTemperatura : quantidadeTemperatura;
   pg			= ( qtdDadosTemperatura > quantidadeTemperatura ) ? Math.ceil( qtdDadosTemperatura/quantidadeTemperatura ) : '1';
   $('#info-pagina-dtr').html( '1/'+pg );
   escreveDadosAcompanhamentoTemperatura( dadosTemperatura, valorIniTemperatura, valorFimTemperatura );

   setaIntervaloTemperatura = window.setInterval(
		function (){
			if( qtdDadosTemperatura > valorFimTemperatura ){
				paginaTemperatura++;
				calculaPaginaTemperatura();
			} else {
				window.clearInterval(setaIntervaloTemperatura);
				carregaDadosAcompanhamentoTemperatura(quantidadeTemperatura, intervaloTemperatura, codigo_cliente, cliente_tipo);
			}
		},
		tempo
	);

}


function escreveDadosAcompanhamentoTemperatura( dados, valIni, valFim ) {

    var html, placa, pos, sm, temperatura, indice;

    for (var i = valIni; i < valFim; i++) {

		id_temp = parseInt(i)+1;
		placa = "'"+dados[i]['placa']+"',"+"'"+dados[i]['data_inicial']+"',"+"'"+dados[i]['data_final']+"'";
		pos   = "'"+dados[i]['latitude']+"',"+"'"+dados[i]['longitude']+"',"+"'"+dados[i]['placa']+"'";
		sm  = "'"+dados[i]['sm']+"'";
		indice = (dados[i]['indice'] == '') ? '' : dados[i]['indice'] + '%';

		html += '<tr>';
			html += '<td>'+id_temp+'</td>';
			html += '<td><a href="javascript:void(0)" onclick="consulta_sm('+sm+')">'+dados[i]['sm']+'</a></td>';
			html += '<td><a href="javascript:void(0)" onclick="eventos_logisticos_sm('+placa+')">'+dados[i]['placa']+'</a></td>';
			html += '<td class="numeric">'+dados[i]['ultima_temperatura']+'</td>';
			html += '<td>'+dados[i]['data_temperatura']+'</td>';
			html += '<td><a href="javascript:void(0)" onclick="mapa_coordenadas('+pos+')">'+dados[i]['posicao']+'</a></td>';
			html += '<td>'+indice+'</td>';
			html +=
			'<td>'+((dados[i]['posicionamento'] == 's') ? '<span class="badge-empty badge badge-success" title="Posicionamento Normal"></span>' +
				((dados[i]['temperatura_violada'] == 's') ? '<span class="badge-empty badge badge-important" title="Temperatura fora da faixa"></span>' : '<span class="badge-empty badge badge-success" title="Temperatura dentro da faixa"></span>')
				: '<span class="badge-empty badge " title="Sem Posicionamento"></span>'+'<span class="badge-empty badge " title=""></span>')


			+'</td>';
		html += '</tr>';
	}

	$('#dados-temperatura').html(html);
	$('#nagevacao').css('display', 'block');
	$('#info-temperatura').css('visibility', 'visible');
	$('#info-pagina-dtr').html( paginaTemperatura+'/'+pg );
}

function calculaPaginaTemperatura() {

	valorIniTemperatura = valorFimTemperatura;
	valorFimTemperatura = ( ( paginaTemperatura * quantidadeTemperatura ) >= qtdDadosTemperatura ) ? qtdDadosTemperatura : ( paginaTemperatura * quantidadeTemperatura );
	escreveDadosAcompanhamentoTemperatura( dadosTemperatura, valorIniTemperatura, valorFimTemperatura );
}

function acompanhamentoTemperaturaNext(){

	if( qtdDadosTemperatura > valorFimTemperatura ) {
		paginaTemperatura++;
		calculaPaginaTemperatura();
	}
}

function acompanhamentoTemperaturaPrev(){

	if( valorIniTemperatura > 0 ) {
		paginaTemperatura--;
		valorIniTemperatura = valorIniTemperatura - quantidadeTemperatura;
		valorFimTemperatura = (valorFimTemperatura == qtdDadosTemperatura ) ? parseInt(valorIniTemperatura) + parseInt(quantidadeTemperatura) : valorFimTemperatura - quantidadeTemperatura;
		escreveDadosAcompanhamentoTemperatura( dadosTemperatura, valorIniTemperatura, valorFimTemperatura );
	}
}

function tempos_por_placa(placa, data_inicial, data_final) {
	var div = jQuery('div#tempos_por_placa');
	$.ajax({
		type: 'POST',
		url: '/portal/veiculos/tempos_por_placa',
		cache: false,
		data:{
			"data[TVeicVeiculo][placa]" : placa,
			"data[TVeicVeiculo][data_inicio_real]" : data_inicial + ' 00:00:00',
			"data[TVeicVeiculo][data_final_real]" : data_final + ' 23:59:59'
		},
		beforeSend : function(){
			bloquearDiv(div);
		},
		success : function(data){
			div.html(data);
			div.unblock();
		},
		error : function(){
			div.unblock();
		}
	});
}

function eventos_logisticos_por_placa(placa, data_inicial, data_final) {
	var div = jQuery('div#eventos');
	$.ajax({
		type: 'POST',
		url: '/portal/eventos_sistema/eventos_logisticos',
		cache: false,
		data:{
			"data[TEsisEventoSistema][placa]" : placa,
			"data[TEsisEventoSistema][data_inicial]" : data_inicial + ' 00:00:00',
			"data[TEsisEventoSistema][data_final]" : data_final + ' 23:59:59'
		},
		beforeSend : function(){
			bloquearDiv(div);
		},
		success : function(data){
			div.html(data);
			div.unblock();
		},
		error : function(){
			div.unblock();
		}
	});
}

function macros_por_placa(placa, data_inicial, data_final) {
	var div = jQuery('div#macros_por_placa');
	$.ajax({
		type: 'POST',
		url: '/portal/veiculos/macros_por_placa',
		cache: false,
		data:{
			"data[TVeicVeiculo][placa]" : placa,
			"data[TVeicVeiculo][data_inicio_real]" : data_inicial + ' 00:00:00',
			"data[TVeicVeiculo][data_final_real]" : data_final + ' 23:59:59'
		},
		beforeSend : function(){
			bloquearDiv(div);
		},
		success : function(data){
			div.html(data);
			div.unblock();
		},
		error : function(){
			div.unblock();
		}
	});
}

function ultima_posicao(placa) {
	var div = jQuery('div#ultima_posicao');
	$.ajax({
		type: 'POST',
		url: '/portal/veiculos/ultima_posicao',
		cache: false,
		data:{
			"data[TVeicVeiculo][placa]" : placa
		},
		beforeSend : function(){
			bloquearDiv(div);
		},
		success : function(data){
			div.html(data);
			div.unblock();
		},
		error : function(){
			div.unblock();
		}
	});
}

function carregar_mensagem_livre(this_data,conteiner){
	var conteiner = $(conteiner);
	$.ajax({
		type: 'post',
		url: baseUrl + 'terminais/carregar_mensagem_livre/' + Math.random(),
		data: {'dados':this_data},
		beforeSend : function(){
			bloquearDiv(conteiner);
		},
		success: function(data){
			conteiner.html(data);
		},
		error: function(erro,objeto,qualquercoisa){
			alert(erro+' - '+objeto+' - '+qualquercoisa);
		}
	});
}

function carregar_sensores(placa,conteiner){
	var conteiner = $(conteiner);
	$.ajax({
		type: 'get',
		url: baseUrl + 'ultimos_recebimentos_perifericos/por_placa/'+placa+'/' + Math.random(),
		beforeSend : function(){
			bloquearDiv(conteiner);
		},
		success: function(data){
			conteiner.html(data);
		},
		error: function(erro,objeto,qualquercoisa){
			alert(erro+' - '+objeto+' - '+qualquercoisa);
		}
	});
}

function carrega_dados_temperatura_sm(placa, data_inicial, data_final, temperatura, temperatura2) {

    $.ajax({
        type: 'POST',
        url: '/portal/veiculos/temperaturas_por_placa',
        cache: false,
        data:{
            "data[TVeicVeiculo][placa]" : placa,
            "data[TVeicVeiculo][data_inicio_real]" : data_inicial,
            "data[TVeicVeiculo][data_final_real]" : data_final,
            "data[TVeicVeiculo][Temperatura]" : temperatura,
            "data[TVeicVeiculo][Temperatura2]" : temperatura2
        },
        beforeSend : function(){
            $('#icon-temperatura').html("<img src='/portal/img/loading.gif' title='carregando...' />");
        },
        success : function(data){
            var janela = window_sizes();
            var newwindow = window.open('/portal/veiculos/temperaturas_por_placa','_blank', 'top=0,left=0,width='+(janela.width-80).toString()+',height='+(janela.height)+',scrollbars=yes');
            newwindow.document.write(data);
            $('#icon-temperatura').html('<img src="/portal/img/icon-thermometer.jpg" title="Histórico de Temperatura" />');
        },
        error : function(){

        }
    });
}

function init_combo_event_tipo_alvo(model, input_target_id, input_source_id, somente_cd){
	if (typeof somente_cd == "undefined") {
		somente_cd = 0;
	}
	jQuery(input_source_id).change(function(){
		bloquearDiv(jQuery(input_target_id));
		jQuery(input_target_id).css("color","#000"); // change font-color to black
		jQuery(input_target_id+' option:selected').text('Aguarde, carregando...');
		jQuery(input_target_id+' .lista-cds').html('Aguarde, carregando...');
		jQuery.ajax({
			"url": baseUrl + "relatorios_sm/render_alvos_bandeiras_regioes/" + model + "/" + jQuery(input_source_id).val() + "/" + somente_cd + "/" + Math.random(),
			"success": function(data) {
				jQuery(input_target_id).html(data).change();
				jQuery(input_target_id).css("color","#555555"); // return default font-color
				jQuery(input_target_id).unblock();
			}
		});
	});
}

function init_combo_event_tipo_alvo_emb_transp(model, div_emb_transp, input_target_id, input_embarcador_id, input_transportador_id, somente_cd){
	if (typeof somente_cd == "undefined") {
		somente_cd = 0;
	}
	var div = $(div_emb_transp);
	var inputs = div.find("input:text");
	jQuery(inputs).change(function(){
		jQuery(input_target_id).css("color","#000"); // change font-color to black
		jQuery(input_target_id+' option:selected').text('Aguarde, carregando...');
		jQuery(input_target_id+' .lista-cds').html('Aguarde, carregando...');
		jQuery.ajax({
			"url": baseUrl + "relatorios_sm/render_alvos_bandeiras_regioes_emb_transp/" + model + "/" + inputs[0].value + "/" + inputs[1].value + "/" + somente_cd + "/" + Math.random(),
			"success": function(data) {
				jQuery(input_target_id).html(data).change();
				jQuery(input_target_id).css("color","#555555"); // return default font-color
			}
		});
	});
}



function init_combo_event_alvos_origem(model, input_target_id, input_source_id, selected){
	if (typeof somente_cd == "undefined") {
		somente_cd = 0;
	}
	if (typeof selected == "undefined") {
		selected = '';
	}
	jQuery(input_source_id).blur(function(){
		jQuery(input_target_id).css("color","#000"); // change font-color to black
		jQuery(input_target_id+' option:selected').text('Aguarde, carregando...');
		jQuery(input_target_id+' .lista-cds').html('Aguarde, carregando...');
		jQuery.ajax({
			"type":"post",
			"url": baseUrl + "relatorios_sm/render_alvos_origem/" + model + "/" + jQuery(input_source_id).val() + "/" + Math.random(),
			"data":{
				'data[RelatorioSm][cd_id]':selected.split(',')
			},
			"success": function(data) {
				jQuery(input_target_id).html(data).change();
				jQuery(input_target_id).css("color","#555555"); // return default font-color
			}
		});
	});
}

function busca_dados_motorista(cpf){
	if(cpf){
		$('#RecebsmNome').val('Aguarde...');
		$.ajax({
			url: baseUrl + 'solicitacoes_monitoramento/busca_dados_motorista/'+ cpf +'/'+ Math.random(),
			type: 'post',
			dataType: 'json',
			success: function(data){
				if(jQuery('.motorista-data .documento .error-message').html() == 'Motorista não cadastrado' || jQuery('.motorista-data .documento .error-message').html() == 'DESCONHECIDO')
					jQuery('.motorista-data .documento .error-message').remove();
				jQuery('.motorista-data .motorista-nao-encontrado').remove();
				if(data && data.nome){
					$('#ProfissionalCodigo').val(data.codigo);
					$('#ProfissionalEstrangeiro').val(data.estrangeiro);
					$('#RecebsmNome').val(data.nome);
					$('#RecebsmTelefone').val(data.telefone);
					$('#RecebsmRadio').val(data.radio);
				}else{
					$('#RecebsmNome').val('');

					var a = $("<a class='btn btn-mini btn-primary'>Adicionar Motorista</a>").click(function(event){
						open_dialog(baseUrl + 'profissionais/incluir/' + cpf, 'Adicionar motorista', 572)
						return false;
					});
					jQuery('.motorista-data').append(jQuery('<div class="control-group error motorista-nao-encontrado" style="clear:both">').append("<div class='help-inline' style='padding: 0;'>Motorista não cadastrado</div> ").append(a));
				}
			}

		});
	}
}

function pegar_dados_modal_para_tela(){
	if(jQuery('#modal_dialog .alert-success').length > 0){
		jQuery('#RecebsmCodigoDocumento').val(jQuery('#ProfissionalMotoristaCpf').val());
		jQuery('#RecebsmNome').val(jQuery('#ProfissionalMotoristaNome').val());
		jQuery('#RecebsmTelefone').val(jQuery('#ProfissionalTelefone').val());
		jQuery('#RecebsmRadio').val(jQuery('#ProfissionalRadio').val());
		jQuery('.motorista-data .motorista-nao-encontrado').remove();
		close_dialog();
	}
}

function close_dialog_referencia_success(){
	if(jQuery('#modal_dialog .alert-success').length > 0){
		close_dialog();
	}
}

function reprocessar_arquivo_log_integracao(arquivo,id_label,id_log) {

    $.ajax({
        type: 'POST',
        url: '/portal/logs_integracoes/reprocessar_arquivo_log_integracao/'+arquivo+'/'+id_log+'/'+Math.random(),
        cache: false,
        beforeSend : function(){
            $('.acao-'+id_label).html("<img src='/portal/img/loading.gif' title='aguarde...' />");
        },
        success : function(data){
        	var msg = '';
        	$('.acao-'+id_label).html('');
        	if( data == 0 )
        		msg = '<span class="label label-success">Arquivo enviado para lista de processamento!';
        	else
        		msg = '<span class="label label-important">Erro ao tentar reprocessar o arquivo!';
        	$('.acao-'+id_label).html(msg);
        	var reprocessar = window.setInterval(function(){
        			$('.acao-'+id_label).hide('slow');
        			window.clearInterval(reprocessar);
        		},
        		7000
        	);

        },
        error : function(){

        }
    });
}

function carregaDadosSMSemOperador(intervalo){
	$.ajaxSetup({ cache: false });
		$.ajax({
			type: 'POST',
			dataType: 'JSON',
			url: '/portal/solicitacoes_monitoramento/sm_sem_operador/' + Math.random(),
			cache:false,
			data:{
				'intervalo':intervalo
		},

		beforeSend : function(){
			window.clearInterval(intervalo);
			bloquearDiv(jQuery('.lista-sem-operador'));
		},

		success : function(data) {
			var contagem   = data.contagem;		
			var html;
			$('#total-sem-operador').html('<a href="javascript:sem_operador()">'+contagem+'</a>');
			$('#sm-sem-operador').css( 'visibility', 'visible' );		
			jQuery('.lista-sem-operador').unblock();
		},
		error : function() {
			carregaDadosSMSemOperador(intervalo);
		}
	});
}