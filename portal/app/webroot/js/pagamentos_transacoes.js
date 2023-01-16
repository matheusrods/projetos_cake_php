jQuery(document).ready(function() {
    setup_datepicker();
});

function listar_titulos_pagos_por_centro_custo(Model,centro_custo,centro_custo_desc,dtInicio,dtFim,grupo_empresa,empresa){
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="'+Model+'ListarTitulosPagosPorCodigoTipoFluxoForm" action="/portal/pagamentos_transacoes/listar_titulos_pagos_por_centro_custo">'
    form += '<input type="text" id="'+Model+'Ccusto" value="" name="data['+Model+'][ccusto]">';
    form += '<input type="text" id="'+Model+'CentroCustoDesc" value="" name="data['+Model+'][centro_custo_desc]">';
    form += '<input type="text" id="'+Model+'DataInicial" value="" name="data['+Model+'][data_inicial]">';
    form += '<input type="text" id="'+Model+'DataFinal" value="" name="data['+Model+'][data_final]">';
    form += '<input type="text" id="'+Model+'GrupoEmpresa" value="" name="data['+Model+'][grupo_empresa]">';
    form += '<input type="text" id="'+Model+'Empresa" value="" name="data['+Model+'][empresa]">';
    form += '</form>';
    form += '</div>';
    
    jQuery('body').append(form);
    jQuery("#postlink #"+Model+"Ccusto").val(centro_custo);
    jQuery("#postlink #"+Model+"CentroCustoDesc").val(centro_custo_desc);
    jQuery("#postlink #"+Model+"DataInicial").val(dtInicio);
    jQuery("#postlink #"+Model+"DataFinal").val(dtFim);
    jQuery("#postlink #"+Model+"GrupoEmpresa").val(grupo_empresa);
    jQuery("#postlink #"+Model+"Empresa").val(empresa);

    jQuery("#postlink form").submit();
}

function listar_titulos_pagos_por_centro_custo_sub_codigo(Model,centro_custo,centro_custo_desc,sub_codigo,sub_codigo_desc,dtInicio,dtFim,grupo_empresa,empresa){
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="'+Model+'ListarTitulosPagosPorCodigoTipoFluxoForm" action="/portal/pagamentos_transacoes/listar_titulos_pagos_por_centro_custo_sub_codigo">'
    form += '<input type="text" id="'+Model+'Ccusto" value="" name="data['+Model+'][ccusto]">';
    form += '<input type="text" id="'+Model+'CentroCustoDesc" value="" name="data['+Model+'][centro_custo_desc]">';
    form += '<input type="text" id="'+Model+'SubCodigo" value="" name="data['+Model+'][sub_codigo]">';
    form += '<input type="text" id="'+Model+'SubCodigoDesc" value="" name="data['+Model+'][sub_codigo_desc]">';
    form += '<input type="text" id="'+Model+'DataInicial" value="" name="data['+Model+'][data_inicial]">';
    form += '<input type="text" id="'+Model+'DataFinal" value="" name="data['+Model+'][data_final]">';
    form += '<input type="text" id="'+Model+'GrupoEmpresa" value="" name="data['+Model+'][grupo_empresa]">';
    form += '<input type="text" id="'+Model+'Empresa" value="" name="data['+Model+'][empresa]">';
    form += '</form>';
    form += '</div>';
    
    jQuery('body').append(form);
    jQuery("#postlink #"+Model+"Ccusto").val(centro_custo);
    jQuery("#postlink #"+Model+"CentroCustoDesc").val(centro_custo_desc);
    jQuery("#postlink #"+Model+"SubCodigo").val(sub_codigo);
    jQuery("#postlink #"+Model+"SubCodigoDesc").val(sub_codigo_desc);
    jQuery("#postlink #"+Model+"DataInicial").val(dtInicio);
    jQuery("#postlink #"+Model+"DataFinal").val(dtFim);
    jQuery("#postlink #"+Model+"GrupoEmpresa").val(grupo_empresa);
    jQuery("#postlink #"+Model+"Empresa").val(empresa);
    
    jQuery("#postlink form").submit();
}

function listar_titulos_pagos_por_centro_custo_sub_codigo_conta(Model,centro_custo,centro_custo_desc,sub_codigo,sub_codigo_desc,codigo_conta,codigo_conta_desc,dtInicio,dtFim,grupo_empresa,empresa){
    form = '<div id="postlink" style="display:none;">';
    form += '<form accept-charset="utf-8" method="post" id="'+Model+'ListarTitulosPagosPorCodigoTipoFluxoForm" action="/portal/pagamentos_transacoes/listar_titulos_pagos_por_centro_custo_sub_codigo_conta">'
    form += '<input type="text" id="'+Model+'Ccusto" value="" name="data['+Model+'][ccusto]">';
    form += '<input type="text" id="'+Model+'CentroCustoDesc" value="" name="data['+Model+'][centro_custo_desc]">';
    form += '<input type="text" id="'+Model+'SubCodigo" value="" name="data['+Model+'][sub_codigo]">';
    form += '<input type="text" id="'+Model+'SubCodigoDesc" value="" name="data['+Model+'][sub_codigo_desc]">';
    form += '<input type="text" id="'+Model+'CodigoConta" value="" name="data['+Model+'][codigo_conta]">';
    form += '<input type="text" id="'+Model+'CodigoContaDesc" value="" name="data['+Model+'][codigo_conta_desc]">';
    form += '<input type="text" id="'+Model+'DataInicial" value="" name="data['+Model+'][data_inicial]">';
    form += '<input type="text" id="'+Model+'DataFinal" value="" name="data['+Model+'][data_final]">';
    form += '<input type="text" id="'+Model+'GrupoEmpresa" value="" name="data['+Model+'][grupo_empresa]">';
    form += '<input type="text" id="'+Model+'Empresa" value="" name="data['+Model+'][empresa]">';
    form += '</form>';
    form += '</div>';
    
    jQuery('body').append(form);
    jQuery("#postlink #"+Model+"Ccusto").val(centro_custo);
    jQuery("#postlink #"+Model+"CentroCustoDesc").val(centro_custo_desc);
    jQuery("#postlink #"+Model+"SubCodigo").val(sub_codigo);
    jQuery("#postlink #"+Model+"SubCodigoDesc").val(sub_codigo_desc);
    jQuery("#postlink #"+Model+"CodigoConta").val(codigo_conta);
    jQuery("#postlink #"+Model+"CodigoContaDesc").val(codigo_conta_desc);
    jQuery("#postlink #"+Model+"DataInicial").val(dtInicio);
    jQuery("#postlink #"+Model+"DataFinal").val(dtFim);
    jQuery("#postlink #"+Model+"GrupoEmpresa").val(grupo_empresa);
    jQuery("#postlink #"+Model+"Empresa").val(empresa);
    
    jQuery("#postlink form").submit();
}