function atualizarListaEmailsFinanceiros(codigo_cliente){
    div = jQuery('div.lista');
    bloquearDiv(div);
    div.load(baseUrl + 'clientes/listar_emails_financeiros/' + codigo_cliente + '/' + Math.random() );
}