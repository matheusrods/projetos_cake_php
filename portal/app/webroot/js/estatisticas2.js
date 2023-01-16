$(document).ready(function(){
	setup_datepicker();
})

function estatistica_por_agrupamento(data,agrupamento){
	tipo = jQuery('#TEviaEstaViagemTipo').val();
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" action="/portal/estatisticas_viagens/por_agrupamento">'
    form += '<input type="text" id="TEviaEstaViagemAgrupamento" value="" name="data[TEviaEstaViagem][agrupamento]">';
    form += '<input type="text" id="TEviaEstaViagemTipo" value="" name="data[TEviaEstaViagem][tipo]">';
    form += '<input type="text" id="TEviaEstaViagemData" value="" name="data[TEviaEstaViagem][data]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoEmbarcador" value="" name="data[TEviaEstaViagem][codigo_embarcador]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoTransportador" value="" name="data[TEviaEstaViagem][codigo_transportador]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoSeguradora" value="" name="data[TEviaEstaViagem][codigo_seguradora]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretoraPjur" value="" name="data[TEviaEstaViagem][codigo_corretora_pjur]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretora" value="" name="data[TEviaEstaViagem][codigo_corretora]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretoraVisual" value="" name="data[TEviaEstaViagem][codigo_corretora_visual]">';
    form += '<input type="text" id="TEviaEstaViagemTecnCodigo" value="" name="data[TEviaEstaViagem][tecn_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemUsuaOrasCodigo" value="" name="data[TEviaEstaViagem][usua_oras_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemTipoCodigo" value="" name="data[TEviaEstaViagem][tipo_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemAbertura" value="0" name="data[TEviaEstaViagem][abertura]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink #TEviaEstaViagemTipo").val(tipo);
    jQuery("#postlink #TEviaEstaViagemData").val(data);
    jQuery("#postlink #TEviaEstaViagemAgrupamento").val(agrupamento);
    jQuery("#postlink form").submit();
}

function estatistica_por_agrupamento_e_filtros(agrupamento,tipo_codigo,codigo){
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" action="/portal/estatisticas_viagens/por_agrupamento">'
    form += '<input type="text" id="TEviaEstaViagemAgrupamento" value="" name="data[TEviaEstaViagem][agrupamento]">';
    form += '<input type="text" id="TEviaEstaViagemTipo" value="" name="data[TEviaEstaViagem][tipo]">';
    form += '<input type="text" id="TEviaEstaViagemData" value="" name="data[TEviaEstaViagem][data]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoEmbarcador" value="" name="data[TEviaEstaViagem][codigo_embarcador]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoTransportador" value="" name="data[TEviaEstaViagem][codigo_transportador]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoSeguradora" value="" name="data[TEviaEstaViagem][codigo_seguradora]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretoraPjur" value="" name="data[TEviaEstaViagem][codigo_corretora_pjur]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretora" value="" name="data[TEviaEstaViagem][codigo_corretora]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretoraVisual" value="" name="data[TEviaEstaViagem][codigo_corretora_visual]">';
    form += '<input type="text" id="TEviaEstaViagemTecnCodigo" value="" name="data[TEviaEstaViagem][tecn_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemUsuaOrasCodigo" value="" name="data[TEviaEstaViagem][usua_oras_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemTipoCodigo" value="'+tipo_codigo+'" name="data[TEviaEstaViagem][tipo_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemAbertura" value="1" name="data[TEviaEstaViagem][abertura]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink #TEviaEstaViagemTipo").val(jQuery('#TEviaEstaViagemTipo').val());
    jQuery("#postlink #TEviaEstaViagemData").val(jQuery('#TEviaEstaViagemData').val());
    jQuery("#postlink #TEviaEstaViagemCodigoEmbarcador").val(jQuery('#TEviaEstaViagemCodigoEmbarcador').val());
    jQuery("#postlink #TEviaEstaViagemCodigoTransportador").val(jQuery('#TEviaEstaViagemCodigoTransportador').val());
    jQuery("#postlink #TEviaEstaViagemCodigoSeguradora").val(jQuery('#TEviaEstaViagemCodigoSeguradora').val());
    jQuery("#postlink #TEviaEstaViagemCodigoCorretoraPjur").val(jQuery('#TEviaEstaViagemCodigoCorretoraPjur').val());
    jQuery("#postlink #TEviaEstaViagemCodigoCorretora").val(jQuery('#TEviaEstaViagemCodigoCorretora').val());
    jQuery("#postlink #TEviaEstaViagemCodigoCorretoraVisual").val(jQuery('#TEviaEstaViagemCodigoCorretoraVisual').val());
    jQuery("#postlink #TEviaEstaViagemTecnCodigo").val(jQuery('#TEviaEstaViagemTecnCodigo').val());
    jQuery("#postlink #TEviaEstaViagemUsuaOrasCodigo").val(jQuery('#TEviaEstaViagemUsuaOrasCodigo').val());
    jQuery("#postlink #TEviaEstaViagemAgrupamento").val(agrupamento);
    switch(tipo_codigo){
    	case "1": jQuery("#postlink #TEviaEstaViagemCodigoEmbarcador").val(codigo); break;
    	case "2": jQuery("#postlink #TEviaEstaViagemCodigoTransportador").val(codigo); break;
    	case "3": jQuery("#postlink #TEviaEstaViagemCodigoSeguradora").val(codigo); break;
    	case "4": jQuery("#postlink #TEviaEstaViagemCodigoCorretoraPjur").val(codigo); break;
    	case "5": jQuery("#postlink #TEviaEstaViagemTecnCodigo").val(codigo); break;
    	case "6": jQuery("#postlink #TEviaEstaViagemUsuaOrasCodigo").val(codigo); break;
    }
    jQuery("#postlink form").submit();
}

function listar_sms(data,tipo_viagem){
	form_id = ('formresult' + Math.random()).replace('.','');
	$("#postlink").remove();
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" target="'+form_id+'" action="/portal/estatisticas_viagens/listar_sms">'
    form += '<input type="text" id="TEviaEstaViagemAgrupamento" value="" name="data[TEviaEstaViagem][agrupamento]">';
    form += '<input type="text" id="TEviaEstaViagemTipo" value="" name="data[TEviaEstaViagem][tipo]">';
    form += '<input type="text" id="TEviaEstaViagemData" value="" name="data[TEviaEstaViagem][data]">';
    form += '<input type="text" id="TEviaEstaViagemTipoViagem" value="" name="data[TEviaEstaViagem][tipo_viagem]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoEmbarcador" value="" name="data[TEviaEstaViagem][codigo_embarcador]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoTransportador" value="" name="data[TEviaEstaViagem][codigo_transportador]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoSeguradora" value="" name="data[TEviaEstaViagem][codigo_seguradora]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretoraPjur" value="" name="data[TEviaEstaViagem][codigo_corretora_pjur]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretora" value="" name="data[TEviaEstaViagem][codigo_corretora]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretoraVisual" value="" name="data[TEviaEstaViagem][codigo_corretora_visual]">';
    form += '<input type="text" id="TEviaEstaViagemTecnCodigo" value="" name="data[TEviaEstaViagem][tecn_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemUsuaOrasCodigo" value="" name="data[TEviaEstaViagem][usua_oras_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemTipoCodigo" value="" name="data[TEviaEstaViagem][tipo_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemAbertura" value="0" name="data[TEviaEstaViagem][abertura]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink #TEviaEstaViagemData").val(data);
    jQuery("#postlink #TEviaEstaViagemTipoViagem").val(tipo_viagem);
    var janela = window_sizes();
	window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
    jQuery("#postlink form").submit();
}

function listar_sms_agrupamento(tipo_viagem,tipo_codigo,codigo){
	form_id = ('formresult' + Math.random()).replace('.','');
	$("#postlink").remove();
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" target="'+form_id+'" action="/portal/estatisticas_viagens/listar_sms">'
    form += '<input type="text" id="TEviaEstaViagemAgrupamento" value="" name="data[TEviaEstaViagem][agrupamento]">';
    form += '<input type="text" id="TEviaEstaViagemTipo" value="" name="data[TEviaEstaViagem][tipo]">';
    form += '<input type="text" id="TEviaEstaViagemTipoViagem" value="" name="data[TEviaEstaViagem][tipo_viagem]">';
    form += '<input type="text" id="TEviaEstaViagemData" value="" name="data[TEviaEstaViagem][data]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoEmbarcador" value="" name="data[TEviaEstaViagem][codigo_embarcador]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoTransportador" value="" name="data[TEviaEstaViagem][codigo_transportador]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoSeguradora" value="" name="data[TEviaEstaViagem][codigo_seguradora]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretoraPjur" value="" name="data[TEviaEstaViagem][codigo_corretora_pjur]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretora" value="" name="data[TEviaEstaViagem][codigo_corretora]">';
    form += '<input type="text" id="TEviaEstaViagemCodigoCorretoraVisual" value="" name="data[TEviaEstaViagem][codigo_corretora_visual]">';
    form += '<input type="text" id="TEviaEstaViagemTecnCodigo" value="" name="data[TEviaEstaViagem][tecn_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemUsuaOrasCodigo" value="" name="data[TEviaEstaViagem][usua_oras_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemTipoCodigo" value="'+tipo_codigo+'" name="data[TEviaEstaViagem][tipo_codigo]">';
    form += '<input type="text" id="TEviaEstaViagemAbertura" value="1" name="data[TEviaEstaViagem][abertura]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink #TEviaEstaViagemData").val(jQuery('#TEviaEstaViagemData').val());
    jQuery("#postlink #TEviaEstaViagemCodigoEmbarcador").val(jQuery('#TEviaEstaViagemCodigoEmbarcador').val());
    jQuery("#postlink #TEviaEstaViagemCodigoTransportador").val(jQuery('#TEviaEstaViagemCodigoTransportador').val());
    jQuery("#postlink #TEviaEstaViagemCodigoSeguradora").val(jQuery('#TEviaEstaViagemCodigoSeguradora').val());
    jQuery("#postlink #TEviaEstaViagemCodigoCorretoraPjur").val(jQuery('#TEviaEstaViagemCodigoCorretoraPjur').val());
    jQuery("#postlink #TEviaEstaViagemCodigoCorretora").val(jQuery('#TEviaEstaViagemCodigoCorretora').val());
    jQuery("#postlink #TEviaEstaViagemCodigoCorretoraVisual").val(jQuery('#TEviaEstaViagemCodigoCorretoraVisual').val());
    jQuery("#postlink #TEviaEstaViagemTecnCodigo").val(jQuery('#TEviaEstaViagemTecnCodigo').val());
    jQuery("#postlink #TEviaEstaViagemUsuaOrasCodigo").val(jQuery('#TEviaEstaViagemUsuaOrasCodigo').val());
    jQuery("#postlink #TEviaEstaViagemTipoViagem").val(tipo_viagem);
    switch(tipo_codigo){
    	case "1": jQuery("#postlink #TEviaEstaViagemCodigoEmbarcador").val(codigo); break;
    	case "2": jQuery("#postlink #TEviaEstaViagemCodigoTransportador").val(codigo); break;
    	case "3": jQuery("#postlink #TEviaEstaViagemCodigoSeguradora").val(codigo); break;
    	case "4": jQuery("#postlink #TEviaEstaViagemCodigoCorretoraPjur").val(codigo); break;
    	case "5": jQuery("#postlink #TEviaEstaViagemTecnCodigo").val(codigo); break;
    	case "6": jQuery("#postlink #TEviaEstaViagemUsuaOrasCodigo").val(codigo); break;
    }
    var janela = window_sizes();
	window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
    jQuery("#postlink form").submit();
}