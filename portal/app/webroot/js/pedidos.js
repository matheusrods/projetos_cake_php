function por_servico(Model,codigo_cliente_pagador,mes_referencia,ano_referencia,codigo_produto){
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="'+Model+'ListarPedidos" action="/portal/itens_pedidos/por_servico">'
    form += '<input type="text" id="' + Model + 'CodigoClientePagador" value="" name="data[' + Model + '][codigo_cliente_pagador]">';
    form += '<input type="text" id="' + Model + 'MesReferencia"        value="" name="data[' + Model + '][mes_referencia]">';
    form += '<input type="text" id="' + Model + 'AnoReferencia"        value="" name="data[' + Model + '][ano_referencia]">';
    form += '<input type="text" id="' + Model + 'CodigoProduto"        value="" name="data[' + Model + '][codigo_produto]">';
    form += '</form>';
    form += '</div>';
    
    jQuery('body').append(form);
    jQuery("#postlink #"+Model+"CodigoClientePagador").val(codigo_cliente_pagador);
    jQuery("#postlink #"+Model+"MesReferencia").val(mes_referencia);
    jQuery("#postlink #"+Model+"AnoReferencia").val(ano_referencia);
    jQuery("#postlink #"+Model+"CodigoProduto").val(codigo_produto);
    
    jQuery("#postlink form").submit();
}

function por_cliente_detalhe(Model,codigo_cliente_pagador,mes_referencia,ano_referencia,codigo_produto,codigo_servico){
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="'+Model+'ListarPedidos" action="/portal/itens_pedidos/por_cliente_detalhe">'
    form += '<input type="text" id="' + Model + 'CodigoClientePagador" value="" name="data[' + Model + '][codigo_cliente_pagador]">';
    form += '<input type="text" id="' + Model + 'MesReferencia"        value="" name="data[' + Model + '][mes_referencia]">';
    form += '<input type="text" id="' + Model + 'AnoReferencia"        value="" name="data[' + Model + '][ano_referencia]">';
    form += '<input type="text" id="' + Model + 'CodigoProduto"        value="" name="data[' + Model + '][codigo_produto]">';
    form += '<input type="text" id="' + Model + 'CodigoServico"        value="" name="data[' + Model + '][codigo_servico]">';
    form += '</form>';
    form += '</div>';
    
    jQuery('body').append(form);
    jQuery("#postlink #"+Model+"CodigoClientePagador").val(codigo_cliente_pagador);
    jQuery("#postlink #"+Model+"MesReferencia").val(mes_referencia);
    jQuery("#postlink #"+Model+"AnoReferencia").val(ano_referencia);
    jQuery("#postlink #"+Model+"CodigoProduto").val(codigo_produto);
    jQuery("#postlink #"+Model+"CodigoServico").val(codigo_servico);
    
    jQuery("#postlink form").submit();
}

function por_cliente(Model,codigo_cliente_pagador,mes_referencia,ano_referencia){
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="'+Model+'ListarPedidos" action="/portal/itens_pedidos/por_cliente">'
    form += '<input type="text" id="' + Model + 'CodigoClientePagador" value="" name="data[' + Model + '][codigo_cliente_pagador]">';
    form += '<input type="text" id="' + Model + 'MesReferencia"        value="" name="data[' + Model + '][mes_referencia]">';
    form += '<input type="text" id="' + Model + 'AnoReferencia"        value="" name="data[' + Model + '][ano_referencia]">';
    form += '</form>';
    form += '</div>';
    
    jQuery('body').append(form);
    jQuery("#postlink #"+Model+"CodigoClientePagador").val(codigo_cliente_pagador);
    jQuery("#postlink #"+Model+"MesReferencia").val(mes_referencia);
    jQuery("#postlink #"+Model+"AnoReferencia").val(ano_referencia);
    
    jQuery("#postlink form").submit();
}