jQuery(document).ready(function(){
    codigo_corretora = jQuery("#CorretoraCodigo").val();
    carrega_endereco_corretora(codigo_corretora);
    carrega_contatos_cliente(codigo_corretora);
                
    $(document).on("click", ".dialog", function(e) {
        e.preventDefault();
        open_dialog(this, "Endereço", 960);
    });

    $('.evt-endereco-cep').attr('callback', 'RetornoCep').blur();
});

function Disabled( parent ){
    parent.find('#CorretoraEnderecoCep').parent().find('.alert').remove()

    parent.find('#CorretoraEnderecoEstadoDescricao').val( '' ).removeAttr('readonly')
    parent.find('#CorretoraEnderecoCidade').val( '' ).removeAttr('readonly')
    parent.find('#CorretoraEnderecoBairro').val( '' ).removeAttr('readonly')
    parent.find('#CorretoraEnderecoLogradouro').val( '' )   
}

function RetornoCep( data, obj ){
    let parent = obj.parents('form:eq(0)');
    Disabled( parent )

    if( data ){
        parent.find('#CorretoraEnderecoEstadoDescricao').val( data.VEndereco.endereco_estado_abreviacao ).attr('readonly','readonly')
        parent.find('#CorretoraEnderecoCidade').val( data.VEndereco.endereco_cidade ).attr('readonly','readonly')
        parent.find('#CorretoraEnderecoBairro').val( data.VEndereco.endereco_bairro ).attr('readonly','readonly')
        parent.find('#CorretoraEnderecoLogradouro').val( data.VEndereco.endereco_tipo+' '+data.VEndereco.endereco_logradouro )    
    } else {

        parent.find('#CorretoraEnderecoCep').after('<div class=\'alert\'>CEP Não encontrado</div>');
        setTimeout(function(){
            parent.find('#CorretoraEnderecoCep').parent().find('.alert').remove()
        }, 3000)
    }
    
}

function carrega_endereco_corretora(codigo_corretora) {
	var div = jQuery("#endereco-corretora");
	bloquearDiv(div);
	div.load(baseUrl + 'corretoras_enderecos/listar/' + codigo_corretora+ '/' + Math.random() );
}

function exclui_corretora_endereco(codigo_corretora_endereco, codigo_corretora){
	if (confirm('Deseja realmente excluir ?'))
		jQuery.ajax({
		    type: 'POST',
			url: baseUrl + 'corretoras_enderecos/excluir/' + codigo_corretora_endereco + '/' + Math.random()
			,success: function(data) {
				carrega_endereco_corretora(codigo_corretora);
			}
		});
}

function carrega_contatos_cliente(codigo_corretora,element_div) {
    var div = jQuery(element_div);
    bloquearDiv(div);
    div.load(baseUrl + 'corretoras_contatos/contatos_por_corretoras/' + codigo_corretora + '/' + Math.random() );
}

function excluir_corretora_contato(codigo_corretora_contato, codigo_corretora, element_div) {
    if (confirm('Deseja realmente excluir ?'))
		jQuery.ajax({
		    type: 'POST',
			url: baseUrl + 'corretoras_contatos/excluir/' + codigo_corretora_contato + '/' + Math.random()
			,success: function(data) {
				carrega_contatos_cliente(codigo_corretora,element_div);
			}
		});
}

