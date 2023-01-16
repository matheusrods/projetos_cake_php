function validaDados(codigo_cliente, codigo_setor, codigo_cargo) {
    var span = {text: 'Este campo é obrigatório', class: 'text-error error', css: {position: 'relative', top: '-5px',  marginBottom: '5px', float: 'left'}};
    var ret = true;
    if($.inArray(codigo_cliente.val(), [null, '', undefined]) >= 0) {
        codigo_cliente.parent().find('.error').remove();
        $('<span>', span).insertAfter(codigo_cliente);
        ret = false;
    } else { codigo_cliente.parent().find('.error').remove(); }
    if($.inArray(codigo_setor.val(), [null, '', undefined]) >= 0) {
        codigo_setor.parent().find('.error').remove();
        $('<span>', span).insertAfter(codigo_setor);
        ret = false;
    } else { codigo_setor.parent().find('.error').remove(); }
    if($.inArray(codigo_cargo.val(), [null, '', undefined]) >= 0) {
        codigo_cargo.parent().find('.error').remove();
        $('<span>', span).insertAfter(codigo_cargo);
        ret = false;
    } else { codigo_cargo.parent().find('.error').remove(); }
    return ret;
}
