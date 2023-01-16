function consolidado_teleconsult(codigo_cliente,data_inicial,data_final,codigo_produto,codigo_servico){
    jQuery.post('/portal/fichas/consolidar_relatorio_teleconsult',
    {
        'data[LogFaturamentoTeleconsult][codigo_cliente_utilizador]': codigo_cliente,
        'data[LogFaturamentoTeleconsult][data_inicial]'             : data_inicial,
        'data[LogFaturamentoTeleconsult][data_final]'               : data_final,
        'data[LogFaturamentoTeleconsult][codigo_produto]'           : codigo_produto,
        'data[LogFaturamentoTeleconsult][codigo_servico]'           : codigo_servico
        
    }, function(data){
            generate_modal_dialog();
            jQuery('#modal_dialog').html(data);
            var modal_params_default = {modal: true, resizable: false, width: document.body.offsetWidth - 20, title: 'Relatório Consolidado'};
            jQuery('#modal_dialog').dialog(modal_params_default);
    });
}