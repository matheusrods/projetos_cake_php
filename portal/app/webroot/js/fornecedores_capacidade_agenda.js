var FornecedoresCapacidadeAgenda = new Object();
FornecedoresCapacidadeAgenda = {
	enviaFormAjax : function() {
		
		if($('#ListaDePrecoProdutoServicoCodigo').val()) {
			
			if($('#exame_obrigatorio').length) {
				$('#exame_obrigatorio').remove();
			}
			
			$.ajax({
				url: baseUrl + 'fornecedores_capacidade_agenda/gera_grade', // form action url
			    type: 'POST', // form submit method get/post
			    dataType: 'html', // request type html/json/xml
			    data: $('#FornecedorCapacidadeAgendaIncluirForm').serialize(), // serialize form data
			    beforeSend: function() {
			    	$('#botao').hide();
			    	$('#carregando').show();
			    	$('#agenda').hide();
			    },
			    success: function(retorno) {
			    	$('#agenda').html(retorno).show();
			    },
			    complete: function() {
			    	$('#carregando').hide();
			    	$('#grade').hide();
			    	$('#botao').show();
			    },
			    error: function(e) {
			    	console.log(e);
			    }
			
			});
			
		} else {
			$('#ListaDePrecoProdutoServicoCodigo').after('<div class="alert alert-error" id="exame_obrigatorio">É obrigatório selecionar um exame!</div>');
		}
		
	},
	enviaFormAjaxEditar : function() {
		
		if($('#ListaDePrecoProdutoServicoCodigo').val()) {
			
			if($('#exame_obrigatorio').length) {
				$('#exame_obrigatorio').remove();
			}
			
			$.ajax({
				url: baseUrl + 'fornecedores_capacidade_agenda/gera_grade', // form action url
			    type: 'POST', // form submit method get/post
			    dataType: 'html', // request type html/json/xml
			    data: $('#FornecedorCapacidadeAgendaEditarForm').serialize(), // serialize form data
			    beforeSend: function() {
			    	$('#botao').hide();
			    	$('#carregando').show();
			    	$('#agenda').hide();
			    },
			    success: function(retorno) {
			    	$('#agenda').html(retorno).show();
			    },
			    complete: function() {
			    	$('#carregando').hide();
			    	$('#grade').hide();
			    	$('#botao').show();
			    },
			    error: function(e) {
			    	console.log(e);
			    }
			});
			
		} else {
			$('#ListaDePrecoProdutoServicoCodigo').after('<div class="alert alert-error" id="exame_obrigatorio">É obrigatório selecionar um exame!</div>');
		}			
	},
	enviaFormGradeAjax : function () {
		
		$.ajax({
			url: baseUrl + 'fornecedores_capacidade_agenda/gera_agenda', // form action url
		    type: 'POST', // form submit method get/post
		    dataType: 'html', // request type html/json/xml
		    data: $('#FornecedorGradeAgendaGeraGradeForm').serialize(), // serialize form data
		    beforeSend: function() {
		    	
		    },
		    success: function(retorno) {
		    	$('#quadro_agenda').html(retorno).show();
		    	
		    	$('#agenda_horarios').modal('show');
		    },
		    complete: function() {
		    	
		    },
		    error: function(e) {
		    	console.log(e);
		    }
		});	
	},
	aprova_agenda : function (codigo_fornecedor, codigo_lista_preco_produto_servico) {
		
		$.ajax({
			url: baseUrl + 'fornecedores_capacidade_agenda/grava_agenda/' + codigo_fornecedor + '/' + codigo_lista_preco_produto_servico,
		    type: 'GET',
		    dataType: 'json', // request type html/json/xml
		    beforeSend: function() {
		    	$('#botao_gravar').hide();
		    	$('#carregando_modal').show();
		    },
		    success: function(retorno) {
		    	
		    	if(retorno == '1') {
		    		window.location = document.location.origin + "/portal/fornecedores_capacidade_agenda/agenda_por_exame/" + codigo_fornecedor;
		    	} else {
		    		
			    	$('#botao_gravar').show();
			    	$('#carregando_modal').hide();
			    	
			    	alert('Tente Novamente!');
		    	}
		    },
		    complete: function() {
	    	
		    },
		    error: function(e) {
		    	console.log(e);
		    }
		});
	},
	atualizaStatusAgenda : function (codigo_fornecedor, codigo_lista_preco_produto_servico, status) {
		$.ajax({
	        type: 'POST',
	        url: baseUrl + 'fornecedores_capacidade_agenda/editar_status/' + codigo_fornecedor + '/' + codigo_lista_preco_produto_servico + '/' + status + '/' + Math.random(),
	        beforeSend: function(){
	            bloquearDivSemImg($('div.lista'));  
	        },
	        success: function(data){
	            if(data == 1){
	                $('div.lista').unblock();
	            } else {
	                $('div.lista').unblock();
	            }
	            
	            $("#agenda_por_fornecedor").load('/portal/fornecedores_capacidade_agenda/agenda_por_exame/' + codigo_fornecedor);
	        },
	        error: function(erro){
	            $('div.lista').unblock();
	        }
	    });		
	},
	addPeriodo : function() {
		var id = parseInt( $("#periodos > div").last().attr("id").replace(/[\a-z]+(\_)/g, "") ) + 1;
		
		$("#modelo_periodo .periodos > .periodo").clone().attr("id", "periodo_" + id).appendTo("#periodos").show().find("input, checkbox, select").each(function(index, element){
			$(element).attr("name", $(element).attr("name").replace("X", id));
			$(element).attr("id", $(element).attr("id").replace("X", id));
	    });		
	},	

	getData : function(data, codigo_fornecedor, codigo_lista_preco_produto_servico) {
		$("#data-bloqueio").val(data);
		$(".dia-da-semana").text("");
		$(".js-obtem-horarios").html( 
			$("<div>", {class: "text-center"})
			.append(
				$("<img>", {src: baseUrl + "img/ajax-loader.gif"})
				) 
			);
		$.ajax({
			url: baseUrl + "fornecedores_capacidade_agenda/obtem_horarios_para_bloqueio",
			type: "POST",
			dataType: "json",
			data: {
				data: data,
				codigo_fornecedor: codigo_fornecedor,
				codigo_lista_preco_produto_servico: codigo_lista_preco_produto_servico
			},
		})
		.done(function(response) {
			$(".dia-da-semana").text(response.dia_semana);
			$(".js-obtem-horarios").html(response.html);
		})
	},

	salvarHorarios : function(horarios, diaInteiro, data, codigo_fornecedor, codigo_lista_preco_produto_servico, callback) {
		$.ajax({
			url: baseUrl + 'fornecedores_capacidade_agenda/salvar_horarios',
			type: 'POST',
			dataType: 'json',
			data: {
				horarios: horarios,
				diaInteiro: diaInteiro,
				data: data,
				codigo_fornecedor: codigo_fornecedor,
				codigo_lista_preco_produto_servico: codigo_lista_preco_produto_servico
			},
		})
		.done(function(response) {
			if(response.error) {
				swal({
					type: 'error',
					title: 'Erro',
					text: response.message
				});
			} else {
				callback(response);
			}
		});
	},

	excluiHorario : function(este, codigo, codigo_fornecedor, codigo_lista_preco_produto_servico) {
		este.parents('tr').remove();
		$.ajax({
			url: baseUrl + 'fornecedores_capacidade_agenda/exclui_horario',
			type: 'POST',
			dataType: 'json',
			data: {
				codigo: codigo,
				codigo_fornecedor: codigo_fornecedor,
				codigo_lista_preco_produto_servico: codigo_lista_preco_produto_servico
			},
		})
		.done(function(response) {
			if(response) {
				este.parents('tr').remove();
			}
		});
	}
}