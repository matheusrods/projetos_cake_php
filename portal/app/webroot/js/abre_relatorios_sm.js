function abreRelatorioSm(data_inicial, data_final, codigo_cliente, base_cnpj, tipo_agrupamento, codigo_agrupamento){
    var form_id = ('formresult' + Math.random()).replace('.','');
    var action = '/portal/relatorios_sm/listagem_acompanhamento_viagens_analitico/popup/2';
    if(typeof(tipo_agrupamento) != 'undefined'){
    	action += '/'+codigo_agrupamento+'/'+tipo_agrupamento;
    }else{
    	tipo_agrupamento = 0;
    }
    
    	
    var newForm = jQuery('<form>', {
        'action': action,
        'method': 'post',
        'target': form_id
    }).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][data_inicial]',
        'value': data_inicial,
        'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][data_final]',
        'value': data_final,
        'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][codigo_cliente]',
        'value': codigo_cliente,
        'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][base_cnpj]',
        'value': base_cnpj,
        'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][placa]',
        'value': '',
        'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][placa_carreta]',
        'value': '',
        'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][sm]',
        'value': '',
        'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][pedido_cliente]',
        'value': '',
        'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][loadplan]',
        'value': '',
        'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][nf]',
        'value': '',
        'type': 'hidden'
    })).append(jQuery('<input>', {
    	'name': 'data[RelatorioSm][codigo_status_viagem][]',
    	'value': '3',
    	'type': 'hidden'
    })).append(jQuery('<input>', {
    	'name': 'data[RelatorioSm][codigo_status_viagem][]',
    	'value': '4',
    	'type': 'hidden'
    })).append(jQuery('<input>', {
    	'name': 'data[RelatorioSm][codigo_status_viagem][]',
    	'value': '5',
    	'type': 'hidden'
    })).append(jQuery('<input>', {
    	'name': 'data[RelatorioSm][codigo_status_viagem][]',
    	'value': '6',
        'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][codigo_status_viagem][]',
        'value': '7',
        'type': 'hidden'
    })).append(jQuery('<input>', {
    	'name': 'data[RelatorioSm][codigo_tipo_veiculo]',
    	'value': '',
    	'type': 'hidden'
    })).append(jQuery('<input>', {
    	'name': 'data[RelatorioSm][cd_id]',
    	'value': (tipo_agrupamento == 1 ? codigo_agrupamento : ''),
    	'type': 'hidden'
    })).append(jQuery('<input>', {
    	'name': 'data[RelatorioSm][bandeira_id]',
    	'value': (tipo_agrupamento == 2 ? codigo_agrupamento : ''),
    	'type': 'hidden'
    })).append(jQuery('<input>', {
    	'name': 'data[RelatorioSm][regiao_id]',
    	'value': (tipo_agrupamento == 3 ? codigo_agrupamento : ''),
    	'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][loja_id]',
        'value': (tipo_agrupamento == 4 ? codigo_agrupamento : ''),
        'type': 'hidden'
    })).append(jQuery('<input>', {
        'name': 'data[RelatorioSm][transportador_id]',
        'value': (tipo_agrupamento == 5 ? codigo_agrupamento : ''),
        'type': 'hidden'
    }));
    jQuery('body').append(newForm);
    var janela = window_sizes();
    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
    newForm.submit();
}
