var aplicacao = new Object();
aplicacao = {
	addExame : function() {		
		if($('tbody#lista_aplicacao_exames tr:last-child').length) {
			var id = parseInt($('tbody#lista_aplicacao_exames tr:last-child').attr('id').replace('linha_', '')) + 1;	
		} else {
			id = 0;
		}

		// console.log(id);
		
		$('#modelos #modelo_aplicacao_exames tr').clone().appendTo('tbody#lista_aplicacao_exames').show().find('input, select, radio').each(function(index, element){
			
			if($(element).attr('name')) {
				$(element).attr('name', $(element).attr('name').replace('[X]', '['+ id +']'));
			}
				
			if($(element).attr('id')) {
				$(element).attr('id', $(element).attr('id').replace('X', id));
			}

			if($(element).attr('class')) {
				$(element).attr('class', $(element).attr('class').replace('X', 'input-xlarge'));
				// $(element).addClass("bselect2");
			}

	    });
		
		$('tbody#lista_aplicacao_exames tr:last-child').attr('id', 'linha_' + id);
		$('tbody#lista_aplicacao_exames tr:last-child').find('a').bind( "click", function() {
			aplicacao.removeExame(null, id, this);
		});
		
		$('#AplicacaoExame' + id + 'CodigoExame').bind("change", function() {
			aplicacao.carregaExame(this, id);
		});
		
		setup_mascaras();

		jQuery("select[name='data[AplicacaoExame][codigo_grupo_homogeneo_exame]']").trigger('change');
	},
	removeExame : function(codigo_aplicacao_exame, linha, element) {
		if(codigo_aplicacao_exame) {
			$.ajax({
		        type: "POST",
		        url: "/portal/aplicacao_exames/remove_exame",
				data: "codigo_aplicacao_exame=" + codigo_aplicacao_exame,
		        dataType: "json",
		        beforeSend: function() {
					aplicacao.manipula_modal("modal_carregando", 1);
				},
				error: function(jqXHR, textStatus, errorThrown){
					swal({
						title: 'Algo inesperado aconteceu!',
						text: textStatus + errorThrown,
						icon: 'error'
					});
				},
		        success: function(retorno) {
					$("#linha_" + linha).remove();
		        },
		        complete: function() {
		        	aplicacao.manipula_modal("modal_carregando", 0);
		        }
		    });
			
		} else {
			$(element).parents('tr').remove();
		}
	},		
	manipula_modal : function(id, mostra) {
		if(mostra) {
			$("#" + id).css("z-index", "1050");
			$("#" + id).modal("show");
		} else {
			$(".modal").css("z-index", "-1");
			$("#" + id).modal("hide");
		}
	},
	carregaExame : function(element, chave) {
		$.ajax({
	        type: "POST",
	        url: "/portal/exames/carrega_exame",
			data: "codigo_exame=" + $(element).val(),
	        dataType: "json",
	        beforeSend: function() {
	        	$(element).parents('td').append('<img src="/portal/img/loading.gif" title="carregando..." />');
	        	$('#linha_' + chave).find('input, select, radio').each(function(index, element) { $(element).prop('disabled', 'disabled') });
	        },
	        success: function(retorno) {
	        	$("#AplicacaoExame" + chave + "PeriodoMeses").val(retorno.periodo_meses);
	        	$("#AplicacaoExame" + chave + "PeriodoAposDemissao").val(retorno.periodo_apos_demissao);
	        	
	        	$("#AplicacaoExame" + chave + "ExameAdmissional").prop('checked', (retorno.exame_admissional == '1' ? true : false));
	        	$("#AplicacaoExame" + chave + "ExamePeriodico").prop('checked', (retorno.exame_periodico == '1' ? true : false));
	        	$("#AplicacaoExame" + chave + "ExameDemissional").prop('checked', (retorno.exame_demissional == '1' ? true : false));
	        	$("#AplicacaoExame" + chave + "ExameRetorno").prop('checked', (retorno.exame_retorno == '1' ? true : false));
	        	$("#AplicacaoExame" + chave + "ExameMudanca").prop('checked', (retorno.exame_mudanca == '1' ? true : false));
	        	$("#AplicacaoExame" + chave + "ExameMonitoracao").prop('checked', (retorno.exame_monitoracao == '1' ? true : false));

	        	$("#AplicacaoExame" + chave + "ExameExcluidoConvocacao").prop('checked', (retorno.exame_excluido_convocacao == '1' ? true : false));
	        	$("#AplicacaoExame" + chave + "ExameExcluidoPpp").prop('checked', (retorno.exame_excluido_ppp == '1' ? true : false));
	        	$("#AplicacaoExame" + chave + "ExameExcluidoAso").prop('checked', (retorno.exame_excluido_aso == '1' ? true : false));
	        	$("#AplicacaoExame" + chave + "ExameExcluidoPcmso").prop('checked', (retorno.exame_excluido_pcmso == '1' ? true : false));
	        	$("#AplicacaoExame" + chave + "ExameExcluidoAnual").prop('checked', (retorno.exame_excluido_anual == '1' ? true : false));	        	
	        	
	        	$("#AplicacaoExame" + chave + "PeriodoIdade").val(retorno.periodo_idade);
	        	$("#AplicacaoExame" + chave + "QtdPeriodoIdade").val(retorno.qtd_periodo_idade);
	        	
	        	$("#AplicacaoExame" + chave + "PeriodoIdade2").val(retorno.periodo_idade_2);
	        	$("#AplicacaoExame" + chave + "QtdPeriodoIdade2").val(retorno.qtd_periodo_idade_2);
	        	
	        	$("#AplicacaoExame" + chave + "PeriodoIdade3").val(retorno.periodo_idade_3);
	        	$("#AplicacaoExame" + chave + "QtdPeriodoIdade3").val(retorno.qtd_periodo_idade_3);
	        	
	        	$("#AplicacaoExame" + chave + "PeriodoIdade4").val(retorno.periodo_idade_4);
	        	$("#AplicacaoExame" + chave + "QtdPeriodoIdade4").val(retorno.qtd_periodo_idade_4);
	        },
	        complete: function() {
	        	$('#linha_' + chave).find('input, select, radio').each(function(index, element) { $(element).prop('disabled', '') });
	        	$(element).parents('td').find('img').remove();
	        }
	    });		
	}
}

function altera_ghe_aplicacao_exame(select){
	var codigo = (select.value != '' ? select.value : 'null');

	jQuery("table#grupos_aplicacao_exames tbody tr").each(function(index, element){
		var id = jQuery(element).attr("id").replace('linha_', '');
		var input_codigo = '<input type="hidden" name="data[AplicacaoExame]['+id+'][codigo_grupo_homogeneo_exame]" value="'+codigo+'" id="AplicacaoExame'+id+'CodigoGrupoHomogeneoExame">';
		jQuery("input[name='data[AplicacaoExame]["+id+"][codigo_grupo_homogeneo_exame]']", element).remove();
		jQuery(element).prepend(input_codigo);
	});

}