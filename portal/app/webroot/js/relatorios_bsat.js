function exportar_relatorio( data_inicial, data_final, codigo_cliente, tipo) {
    var form = document.createElement("form");
    var form_id = ('formresult' + Math.random()).replace('.','');
    form.setAttribute('method', 'post');
    form.setAttribute('action', '/portal/relatorios_bsat/relatorio_bsat_'+ tipo +'/export');
    form.setAttribute('target', form_id);
    field = document.createElement('input');
    field.setAttribute('name', 'data[RelatorioBsat][codigo_cliente]');
    field.setAttribute('value', codigo_cliente);
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    field = document.createElement('input');
    field.setAttribute('name', 'data[RelatorioBsat][data_inicial]');
    field.setAttribute('value', data_inicial);
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    field = document.createElement('input');
    field.setAttribute('name', 'data[RelatorioBsat][data_final]');
    field.setAttribute('value', data_final);
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    document.body.appendChild(form);
    var janela = window_sizes();
    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
    form.submit();
}

function exportar_relatorio_historico( codigo_pedido, tipo) {
    var form = document.createElement("form");
    var form_id = ('formresult' + Math.random()).replace('.','');
    form.setAttribute('method', 'post');
    form.setAttribute('action', '/portal/relatorios_bsat/relatorio_bsat_'+ tipo +'_historico/export');
    form.setAttribute('target', form_id);
    field = document.createElement('input');
    field.setAttribute('name', 'data[RelatorioBsat][codigo_pedido]');
    field.setAttribute('value', codigo_pedido);
    field.setAttribute('type', 'hidden');
    form.appendChild(field);
    document.body.appendChild(form);
    var janela = window_sizes();
    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
    form.submit();
}