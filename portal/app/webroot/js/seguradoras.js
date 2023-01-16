jQuery(document).ready(function(){
    codigo_seguradora = jQuery("#SeguradoraCodigo").val();
    carrega_endereco_seguradora(codigo_seguradora);
    carrega_contatos_seguradora(codigo_seguradora);           
    
    $(document).on("click", ".dialog", function(e) {
        e.preventDefault();
        open_dialog(this, "Endereço", 960);
    });

    $('.evt-endereco-cep').attr('callback', 'RetornoCep').blur();
});

function Disabled( parent ){
    parent.find('#SeguradoraEnderecoCep').parent().find('.alert').remove()

    parent.find('#SeguradoraEnderecoEstadoDescricao').val( '' ).removeAttr('readonly')
    parent.find('#SeguradoraEnderecoCidade').val( '' ).removeAttr('readonly')
    parent.find('#SeguradoraEnderecoBairro').val( '' ).removeAttr('readonly')
    parent.find('#SeguradoraEnderecoLogradouro').val( '' )   
}

function RetornoCep( data, obj ){
    let parent = obj.parents('form:eq(0)');
    Disabled( parent )

    if( data ){
        parent.find('#SeguradoraEnderecoEstadoDescricao').val( data.VEndereco.endereco_estado_abreviacao ).attr('readonly','readonly')
        parent.find('#SeguradoraEnderecoCidade').val( data.VEndereco.endereco_cidade ).attr('readonly','readonly')
        parent.find('#SeguradoraEnderecoBairro').val( data.VEndereco.endereco_bairro ).attr('readonly','readonly')
        parent.find('#SeguradoraEnderecoLogradouro').val( data.VEndereco.endereco_tipo+' '+data.VEndereco.endereco_logradouro )    
    } else {

        parent.find('#SeguradoraEnderecoCep').after('<div class=\'alert\'>CEP Não encontrado</div>');
        setTimeout(function(){
            parent.find('#SeguradoraEnderecoCep').parent().find('.alert').remove()
        }, 3000)
    }
    
}

function carrega_endereco_seguradora(codigo_cliente) {
	var div = jQuery("#endereco-seguradora");
	bloquearDiv(div);
	div.load(baseUrl + 'seguradoras_enderecos/listar/' + codigo_seguradora+ '/' + Math.random() );
}

function exclui_seguradora_endereco(codigo_seguradora_endereco, codigo_seguradora){
	if (confirm('Deseja realmente excluir ?'))
		jQuery.ajax({
		    type: 'POST',
			url: baseUrl + 'seguradoras_enderecos/excluir/' + codigo_seguradora_endereco + '/' + Math.random()
			,success: function(data) {
				carrega_endereco_seguradora(codigo_seguradora);
			}
		});
}

function carrega_contatos_seguradora(codigo_seguradora,element_div) {
    var div = jQuery(element_div);
    bloquearDiv(div);
    div.load(baseUrl + 'seguradoras_contatos/contatos_por_seguradoras/' + codigo_seguradora + '/' + Math.random() );
}

function excluir_seguradora_contatos(codigo_seguradora_contato, codigo_seguradora, element_div) {
    if (confirm('Deseja realmente excluir ?'))
		jQuery.ajax({
		    type: 'POST',
			url: baseUrl + 'seguradoras_contatos/excluir/' + codigo_seguradora_contato + '/' + Math.random()
			,success: function(data) {
				carrega_contatos_seguradora(codigo_seguradora,element_div);
			}
		});
}