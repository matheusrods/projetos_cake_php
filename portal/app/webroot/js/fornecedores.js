var fornecedores = new Object();

fornecedores = {
    addHorario : function() {
        // var qtd_horarios = parseInt($('#periodos_horario_diferenciado > table').last().attr('id').replace(/X/g, '')) + 1;
        var qtd_horarios = $('#periodos_horario_diferenciado > table').length;
        
        $('#modelos #horario_periodo #periodos_horario_diferenciado > table')
            .clone()
            // .last()
            .attr('id', 'horarioDif_' + qtd_horarios)
            .appendTo('#periodos_horario_diferenciado')
            .show()
            .find('input, checkbox, select')
            .each(function(index, element){ 
                $(element).attr('name', $(element).attr('name').replace(/X/g, qtd_horarios));
                $(element).attr('id', $(element).attr('id').replace(/X/g, qtd_horarios));
        });     
    },
    addHorarioEdit : function() {
        // var qtd_horarios = parseInt($('#periodos_horario_diferenciado > table').last().attr('id').replace(/X/g, '')) + 1;
        var qtd_horarios = $('#periodos_horario_diferenciado_edit > table').length;
        
        $('#modelos #horario_periodo_edit #periodos_horario_diferenciado_edit > table')
            .clone()
            // .last()
            .attr('id', 'horarioDif_' + qtd_horarios)
            .appendTo('#periodos_horario_diferenciado_edit')
            .show()
            .find('input, checkbox, select')
            .each(function(index, element){
                $(element).attr('name', $(element).attr('name').replace(/X/g, qtd_horarios));
                $(element).attr('id', $(element).attr('id').replace(/X/g, qtd_horarios));
        });     
    },
}



jQuery(document).ready(function(){
    codigo_fornecedor = jQuery("#fornecedorCodigo").val();
    carrega_endereco_fornecedor(codigo_fornecedor);
                
    $(document).on("click", ".dialog", function(e) {
        e.preventDefault();
        open_dialog(this, "Endereço", 960);
    });
    
    $(document).on("click", ".dialog_contato", function(e) {
        e.preventDefault();
        open_dialog(this, "Contatos", 960);
    });

     $(document).on("click", ".dialog_historico", function(e) {
        e.preventDefault();
        open_dialog(this, "Histórico", 960);
    });

	$(document).on("click", ".dialog_documentos", function(e) {
        e.preventDefault();
        open_dialog(this, "Documentos", 910);
    });

    $(document).on("click", ".dialog_horarios", function(e) {
        e.preventDefault();
        open_dialog(this, "Horários", 880);
    });

    $(document).on("click", ".dialog_medicos", function(e) {
        e.preventDefault();
        open_dialog(this, "Médicos", 880);
    });

    $(document).on("click", ".dialog_medicos_fornecedor", function(e) {
        e.preventDefault();
        open_dialog(this, "Médicos", 880);
    });
});

function carrega_fornecedor_horario(codigo_fornecedor) {
	var div = jQuery('#fornecedor-horario-lista');
    bloquearDiv(div);
	div.load(baseUrl + 'fornecedores_horarios/listagem/' + codigo_fornecedor+ '/' + Math.random() );
}

function carrega_endereco_fornecedor(codigo_fornecedor) {
	var div = jQuery("#endereco-fornecedor");
	bloquearDiv(div);
	div.load(baseUrl + 'fornecedores_enderecos/listar/' + codigo_fornecedor+ '/' + Math.random() );
}

function exclui_fornecedor_endereco(codigo_fornecedor_endereco, codigo_fornecedor){
	if (confirm('Deseja realmente excluir ?'))
		jQuery.ajax({
		    type: 'POST',
			url: baseUrl + 'fornecedores_enderecos/excluir/' + codigo_fornecedor_endereco + '/' + Math.random()
			,success: function(data) {
				carrega_endereco_fornecedor(codigo_fornecedor);
			}
		});
}

