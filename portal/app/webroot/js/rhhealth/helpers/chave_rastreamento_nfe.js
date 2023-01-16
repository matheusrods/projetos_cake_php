console.log('chave_rastreamento_nfe carregado');

var chave_rastreamento_nfe = {
    "class-key": '.chave_rastreamento_nfe'
};

$('form').on('click', chave_rastreamento_nfe["class-key"], function() {

    console.log( 'click :' + $(this).val() );
    
});

$('form').on('blur', chave_rastreamento_nfe["class-key"], function() {

    console.log( 'blur :' + $(this).val() );
    
});

$('form').on('keyup', chave_rastreamento_nfe["class-key"], function() {

    console.log( 'keyup :' + $(this).val() );
    
});

$('form').on('change', chave_rastreamento_nfe["class-key"], function() {

    console.log( 'change :' + $(this).val() );
    
});