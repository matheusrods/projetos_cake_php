jQuery(document).ready(function() {
    setup_datepicker();
});

function estatistica_por_operacao(linha) {
    tipo = jQuery('#EstatisticaSmTipo').val();
    hora = jQuery(linha).parent().parent().find('td:first').html();
    if (tipo > 1)
        hora += ' 23:59:59';
    if (jQuery("#postlink").size() > 0)
        jQuery("#postlink").remove();
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="EstatisticaSmEstatisticaGeralForm" action="/portal/estatisticas_sms/por_operacao">'
    form += '<input type="text" id="EstatisticaSmTipo" value="" name="data[EstatisticaSm][tipo]">';
    form += '<input type="text" id="EstatisticaSmData" value="" name="data[EstatisticaSm][data]">';
    form += '<input type="text" id="EstatisticaSmDataInicioFim" value="true" name="data[EstatisticaSm][data_inicio_fim]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink #EstatisticaSmTipo").val(tipo);
    jQuery("#postlink #EstatisticaSmData").val(hora);
    jQuery("#postlink form").submit();
	
}

function estatistica_por_operador(codigo_tipo_operacao) {
    tipo = jQuery('#EstatisticaSmTipo').val();
    hora = jQuery('#EstatisticaSmHora').val();
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="EstatisticaSmEstatisticaGeralForm" action="/portal/estatisticas_sms/por_operador">'
    form += '<input type="text" id="EstatisticaSmCodigoTipoOperacao" value="" name="data[EstatisticaSm][codigo_tipo_operacao]">';
    form += '<input type="text" id="EstatisticaSmTipo" value="" name="data[EstatisticaSm][tipo]">';
    form += '<input type="text" id="EstatisticaSmData" value="" name="data[EstatisticaSm][data]">';
    form += '<input type="text" id="EstatisticaSmStatus" value="2" name="data[EstatisticaSm][status]">';
    form += '<input type="text" id="EstatisticaSmDataInicioFim" value="true" name="data[EstatisticaSm][data_inicio_fim]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink #EstatisticaSmCodigoTipoOperacao").val(codigo_tipo_operacao);
    jQuery("#postlink #EstatisticaSmTipo").val(tipo);
    jQuery("#postlink #EstatisticaSmData").val(hora);
    jQuery("#postlink form").submit();
}

function estatistica_por_cliente(linha) {
    tipo = jQuery('#EstatisticaSmTipo').val();
    hora = jQuery(linha).parent().parent().find('td:first').html();
    if (tipo > 1)
        hora += ' 23:59:59';
    if (jQuery("#postlink").size() > 0)
        jQuery("#postlink").remove();
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="EstatisticaSmEstatisticaGeralForm" action="/portal/estatisticas_sms/por_cliente">'
    form += '<input type="text" id="EstatisticaSmTipo" value="" name="data[EstatisticaSm][tipo]">';
    form += '<input type="text" id="EstatisticaSmData" value="" name="data[EstatisticaSm][data]">';
    form += '<input type="text" id="EstatisticaSmDataInicioFim" value="true" name="data[EstatisticaSm][data_inicio_fim]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink #EstatisticaSmTipo").val(tipo);
    jQuery("#postlink #EstatisticaSmData").val(hora);
    jQuery("#postlink form").submit();
}

function sm_consulta_geral_por_operacao(codigo_tipo_operacao) {
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="EstatisticaSmEstatisticaGeralForm" action="/portal/solicitacoes_monitoramento/pre_filtro_consulta_geral">'
    form += '<input type="text" id="RecebsmCodOperacao" value="" name="data[Recebsm][cod_operacao][]">';
    form += '<input type="text" id="RecebsmStatus" value="1" name="data[Recebsm][status][]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink #RecebsmCodOperacao").val(codigo_tipo_operacao);
    jQuery("#postlink form").submit();
}

function sm_consulta_geral_por_operador( codigo_tipo_operacao, operador, status ) {
    
    var operacao = ( codigo_tipo_operacao == '' ) ? '' : '<input type="text" id="RecebsmCodOperacao" value="" name="data[Recebsm][cod_operacao][]">';    
        
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="EstatisticaSmEstatisticaGeralForm" action="/portal/solicitacoes_monitoramento/pre_filtro_consulta_geral">'
    form += operacao;
    form += '<input type="text" id="RecebsmCodOperador" value="" name="data[Recebsm][cod_operador]">';    
    form += '<input type="text" id="RecebsmStatus" value="" name="data[Recebsm][status][]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink #RecebsmCodOperacao").val(codigo_tipo_operacao);
    jQuery("#postlink #RecebsmCodOperador").val(operador);    
    jQuery("#postlink #RecebsmStatus").val(status);
    jQuery("#postlink form").submit();
}

function sm_consulta_geral_por_operacao_historico( codigo_operacao, data, status, tipo ) {        
        
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="EstatisticaSmEstatisticaGeralForm" action="/portal/solicitacoes_monitoramento/pre_filtro_consulta_geral_historico">'    
    form += '<input type="text" id="EstatisticaSmCodOperacao" value="" name="data[EstatisticaSm][cod_operacao][]">';    
    form += '<input type="text" id="EstatisticaSmStatus" value="" name="data[EstatisticaSm][status][]">';
    form += '<input type="text" id="EstatisticaSmData" value="" name="data[EstatisticaSm][data]">';  
    form += '<input type="text" id="EstatisticaSmTipo" value="" name="data[EstatisticaSm][tipo]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);    
    jQuery("#postlink #EstatisticaSmCodOperacao").val(codigo_operacao);    
    jQuery("#postlink #EstatisticaSmData").val(data);    
    jQuery("#postlink #EstatisticaSmStatus").val(status);  
    jQuery("#postlink #EstatisticaSmTipo").val( tipo );   
    jQuery("#postlink form").submit();
}

function sm_consulta_geral_por_operador_historico( codigo_tipo_operacao, operador, status, data, tipo ) {
    
    var operacao = ( codigo_tipo_operacao == '' ) ? '' : '<input type="text" id="EstatisticaSmCodOperacao" value="" name="data[EstatisticaSm][cod_operacao][]">';    
        
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="EstatisticaSmEstatisticaGeralForm" action="/portal/solicitacoes_monitoramento/pre_filtro_consulta_geral_historico">'
    form += operacao;
    form += '<input type="text" id="EstatisticaSmCodOperador" value="" name="data[EstatisticaSm][cod_operador]">';    
    form += '<input type="text" id="EstatisticaSmStatus" value="" name="data[EstatisticaSm][status][]">';
    form += '<input type="text" id="EstatisticaSmData" value="" name="data[EstatisticaSm][data]">';
    form += '<input type="text" id="EstatisticaSmTipo" value="" name="data[EstatisticaSm][tipo]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink #EstatisticaSmCodOperacao").val(codigo_tipo_operacao);
    jQuery("#postlink #EstatisticaSmCodOperador").val(operador);    
    jQuery("#postlink #EstatisticaSmStatus").val(status);
    jQuery("#postlink #EstatisticaSmData").val(data);
    jQuery("#postlink #EstatisticaSmTipo").val(tipo);    
    jQuery("#postlink form").submit();
}

function sm_consulta_geral_por_tecnologia( codigo_tecnologia, status ) {            
    
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="SolicitacoesMonitoramentoForm" action="/portal/solicitacoes_monitoramento/pre_filtro_consulta_geral">'
    form += '<input type="text" id="RecebsmCodequipamento" value="" name="data[Recebsm][codequipamento][]">';
    form += '<input type="text" id="RecebsmStatus" value="" name="data[Recebsm][status][]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink #RecebsmCodequipamento").val(codigo_tecnologia);
    jQuery("#postlink #RecebsmStatus").val( jQuery(status).val() );
    jQuery("#postlink form").submit();
}

function estatistica_transportadora_tecnologia_por_sm( data_ini, data_fim, cliente_transportador, cliente_embarcador, status) {          
    
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="TranportadoraTecnologiaPorSmForm" action="/portal/solicitacoes_monitoramento/transportadora_tecnologia_por_sm">';
    form += '<input type="text" id="RecebsmDataIni" value="" name="data[Recebsm][data_inicial]">';
    form += '<input type="text" id="RecebsmDataFim" value="" name="data[Recebsm][data_final]">';
    form += '<input type="text" id="RecebsmCodigoTransportadora" value="" name="data[Recebsm][cliente_transportador]">';
    form += '<input type="text" id="RecebsmCodigoEmbarcador" value="" name="data[Recebsm][cliente_embarcador]">';
    form += '<input type="text" id="RecebsmStatus" value="" name="data[Recebsm][status]">';     
    form += '</form>';
    
    jQuery('body').append(form);    
    jQuery("#postlink #RecebsmDataIni").val( data_ini );
    jQuery("#postlink #RecebsmDataFim").val( data_fim );    
    jQuery("#postlink #RecebsmCodigoTransportadora").val( cliente_transportador );    
    jQuery("#postlink #RecebsmCodigoEmbarcador").val( $(cliente_embarcador).val() );
    jQuery("#postlink #RecebsmStatus").val( status );
    jQuery("#postlink form").submit(); 
}

function estatistica_transportadora_sm_consulta_geral_por_tecnologia( data_ini, data_fim, codigo_tecnologia, cliente, cliente_tipo, status ) {          
    
    var clienteTipo = ( cliente_tipo == 'embarcador' ) ? 'data[Recebsm][cliente_embarcador][]' : 'data[Recebsm][cliente_transportador][]';    

    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="TranportadoraTecnologiaPorSmForm" action="/portal/solicitacoes_monitoramento/pre_filtro_consulta_geral">';
    form += '<input type="text" id="RecebsmDataIni" value="" name="data[Recebsm][data_inicial]">';
    form += '<input type="text" id="RecebsmDataFim" value="" name="data[Recebsm][data_final]">';
    form += '<input type="text" id="RecebsmCodequipamento" value="" name="data[Recebsm][codequipamento]">';    
    form += '<input type="text" id="RecebsmStatus" value="" name="data[Recebsm][status][]">';            
    
    valores = retornaValoresComboBox(cliente);                

    for( var i = 0; i < valores.length; i++ ){
        form += '<input type="text" value="'+valores[i]+'" name="'+clienteTipo+'">';            
    }    

    form += '</form>';
    form += '</div>';
    
    jQuery('body').append(form);    
    jQuery("#postlink #RecebsmDataIni").val( data_ini );
    jQuery("#postlink #RecebsmDataFim").val( data_fim );    
    jQuery("#postlink #RecebsmCodequipamento").val( codigo_tecnologia );       
    jQuery("#postlink #RecebsmStatus").val( status );    
    jQuery("#postlink form").submit(); 
}

function estatistica_rma_por_embarcador( data_inicial, data_final, codigo_cliente, cliente_embarcador, cliente_transportadora) {   
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" action="/portal/solicitacoes_monitoramento/rma_por_embarcador">'
    form += '<input type="text" value="' + data_inicial + '" name="data[Recebsm][data_inicial]">';
    form += '<input type="text" value="' + data_final + '" name="data[Recebsm][data_final]">';
    form += '<input type="hidden" value="' + codigo_cliente + '" name="data[Recebsm][codigo_cliente]">';
    form += '<input type="hidden" value="' + cliente_embarcador + '" name="data[Recebsm][cliente_embarcador]">';
    form += '<input type="hidden" value="' + cliente_transportadora + '" name="data[Recebsm][cliente_transportador]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink form").submit(); 
}

function estatistica_rma_por_transportadora( data_inicial, data_final, codigo_cliente, cliente_embarcador, cliente_transportadora) {   
    
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" action="/portal/solicitacoes_monitoramento/rma_por_transportadora">'
    form += '<input type="text" value="' + data_inicial + '" name="data[Recebsm][data_inicial]">';
    form += '<input type="text" value="' + data_final + '" name="data[Recebsm][data_final]">';
    form += '<input type="hidden" value="' + codigo_cliente + '" name="data[Recebsm][codigo_cliente]">';
    form += '<input type="hidden" value="' + cliente_embarcador + '" name="data[Recebsm][cliente_embarcador]">';
    form += '<input type="hidden" value="' + cliente_transportadora + '" name="data[Recebsm][cliente_transportador]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink form").submit(); 
}

function estatistica_rma_por_embarcador_gerador( data_inicial, data_final, codigo_cliente, cliente_embarcador, cliente_transportadora, codigo_gerador_ocorrencia) {
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" action="/portal/solicitacoes_monitoramento/rma_por_embarcador_gerador">'
    form += '<input type="text" value="' + data_inicial + '" name="data[Recebsm][data_inicial]">';
    form += '<input type="text" value="' + data_final + '" name="data[Recebsm][data_final]">';
    form += '<input type="hidden" value="' + codigo_cliente + '" name="data[Recebsm][codigo_cliente]">';
    form += '<input type="hidden" value="' + cliente_embarcador + '" name="data[Recebsm][cliente_embarcador]">';
    form += '<input type="hidden" value="' + cliente_transportadora + '" name="data[Recebsm][cliente_transportador]">';
    form += '<input type="hidden" value="' + codigo_gerador_ocorrencia + '" name="data[Recebsm][codigo_gerador_ocorrencia]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink form").submit(); 
}

function estatistica_rma_por_transportadora_gerador( data_inicial, data_final, codigo_cliente, cliente_embarcador, cliente_transportadora, codigo_gerador_ocorrencia) {
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" action="/portal/solicitacoes_monitoramento/rma_por_transportadora_gerador">'
    form += '<input type="text" value="' + data_inicial + '" name="data[Recebsm][data_inicial]">';
    form += '<input type="text" value="' + data_final + '" name="data[Recebsm][data_final]">';
    form += '<input type="hidden" value="' + codigo_cliente + '" name="data[Recebsm][codigo_cliente]">';
    form += '<input type="hidden" value="' + cliente_embarcador + '" name="data[Recebsm][cliente_embarcador]">';
    form += '<input type="hidden" value="' + cliente_transportadora + '" name="data[Recebsm][cliente_transportador]">';
    form += '<input type="hidden" value="' + codigo_gerador_ocorrencia + '" name="data[Recebsm][codigo_gerador_ocorrencia]">';
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink form").submit(); 
}

function eventos_logisticos_sm( placa, data_inicial, data_final, codigo_cliente) {   
    var form = document.createElement("form");
    var form_id = ('formresult' + Math.random()).replace('.','');
    form.setAttribute("method", "post");

    if(codigo_cliente == "undefined")
    	form.setAttribute("action", "/portal/viagens/eventos_logisticos_por_placa/1");
    else
    	form.setAttribute("action", "/portal/viagens/eventos_logisticos_por_placa/1/"+ codigo_cliente);
    
    form.setAttribute("target", form_id);
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][placa]");
    field.setAttribute("value", placa);
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][data_inicial]");
    field.setAttribute("value", data_inicial.substr(0,10));
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    field = document.createElement("input");
    field.setAttribute("name", "data[TViagViagem][data_final]");
    field.setAttribute("value", data_final.substr(0,10));
    field.setAttribute("type", "hidden");
    form.appendChild(field);
    document.body.appendChild(form);
    var janela = window_sizes();
    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
    form.submit();
}

function detalhes_evento(codigo_evento) {
    var form = document.createElement('form');
    var form_id = ('formresult' + Math.random()).replace('.','');
    form.setAttribute('method', 'post');
    form.setAttribute('action', '/portal/eventos_viagem/detalhes_evento/'+codigo_evento+'/'+Math.random());
    form.setAttribute('target', form_id);
    field = document.createElement('input');
    field.setAttribute('name', 'data[TEspaEventoSistemaPadrao][espa_codigo]');
    field.setAttribute('value', codigo_evento);
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    //field = document.createElement('input');
    //field.setAttribute('name', 'data[TRacsRegraAceiteSm][racs_codigo]');
    //field.setAttribute('value', racs_codigo);
    //field.setAttribute('type', 'hidden');
    form.appendChild(field);
    document.body.appendChild(form);
    var janela = window_sizes();
    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
    form.submit();
}


function estatistica_rma( codigo_cliente, data_inicial, data_final, codigo_gerador_ocorrencia , codigo_ocorrencia, cliente_embarcador, cliente_transportador, tipo_empresa) {
    form = '<div id="postlink" style="display:none;">';
        form += '<form accept-charset="utf-8" method="post" action="/portal/solicitacoes_monitoramento/visualizar_rma">';
            form += '<input type="text" value="' + codigo_cliente + '" name="data[MRmaEstatistica][codigo_cliente]">';
            form += '<input type="text" value="' + data_inicial + '" name="data[MRmaEstatistica][data_inicial]">';
            form += '<input type="text" value="' + data_final + '" name="data[MRmaEstatistica][data_final]">';
            form += '<input type="text" value="' + codigo_gerador_ocorrencia + '" name="data[MRmaEstatistica][codigo_gerador_ocorrencia]">';
            form += '<input type="text" value="' + codigo_ocorrencia + '" name="data[MRmaEstatistica][codigo_ocorrencia]">';
            form += '<input type="text" value="' + cliente_embarcador + '" name="data[MRmaEstatistica][codigo_embarcador]">';
            form += '<input type="text" value="' + cliente_transportador + '" name="data[MRmaEstatistica][codigo_transportador]">';
            form += '<input type="text" value="' + tipo_empresa + '" name="data[MRmaEstatistica][tipo_empresa]">';
        form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink form").submit();
}