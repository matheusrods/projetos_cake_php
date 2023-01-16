$(document).ready(function() {
	$('[data-toggle="tooltip"]').tooltip();
});

function deleteAlert() {
	$('body').on('click', '.delete-confirm', function(e) {
		e.preventDefault();
		var link = $(this).attr('href');
		swal({   
			title: $(this).attr('data-title'),
			text: $(this).attr('data-text') ,  
			type: "warning",   
			cancelButtonText: 'Cancelar',
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "Excluir",   
			closeOnConfirm: true 
		}, function(){   
			window.location.replace(link); 
		});

	});

}

function executa_questionario() {
	
	$('body').on('click', '.caixa', function(event) {
		$('.check').removeClass('js-bordered');
		$(this).parents('.check').addClass('js-bordered');
		$(this).find('input[type="radio"]').prop('checked', true);
	});

	$('body').on('click', '.js-botao-voltar', function(event) {
		$('.ajaxLoader').fadeIn();
		$.ajax({
			url: baseUrl + 'questionarios/voltar_questao',
			type: 'POST',
			dataType: 'json',
			data: { codigo_questionario: $(this).parents('.app-canvas').attr('data_codigo_questionario') },
		})
		.done(function(response) {
			if(response) {
				location.reload();
			}
		})
	});

	$('body').on('click', '.js-botao-avancar', function(event) {
			//se não selecionou nenhuma resposta explode erro na tela e breca operação
			if($('input[type="radio"]:checked').length == 0) {
				swal({
					type: 'warning',
					title: 'Atenção',
					text: 'Você deve selecionar uma resposta para continuar'
				})
				return false;
			}
			$('.ajaxLoader').fadeIn();
			$.ajax({
				url: baseUrl + 'questionarios/salva_ajax',
				type: 'POST',
				dataType: 'json',
				data: {
					codigo_resposta: $(this).parents('.application').find('input[type="radio"]:checked').val(), 
					codigo_questao: $(this).parents('.application').attr('data_codigo_questao')
				},
			})
			.done(function(response) {
				if(response) {
					if(response.finalizado) {
						$('.app-canvas').addClass('hide');
						swal({
							type: 'success',
							title: 'Obrigado por responder o questionário!',
							text: response.html,
							html: true
						}, function(){   
							$('.ajaxLoader').fadeIn();
							window.location = '/portal/dados_saude/dashboard';
						});
					} else {
						$('.app-canvas').html(response.html);	
					}
				}
				adjust_canvas();
				view_quest();
			})
			.always(function() {
				$('.ajaxLoader').hide();
			});
		});
	$(window).load(function() {
		view_quest();
		adjust_canvas();
	});
}

function pula_primeira_questao(codigo, codigo_questao, callback) {
	$.ajax({
		url: baseUrl + 'questionarios/salva_ajax',
		type: 'POST',
		dataType: 'json',
		data: {
			codigo_resposta: codigo, 
			codigo_questao: codigo_questao,
		},
	})
	.done(function(response) {
		if(response) {
			if(response.finalizado) {
				$('.app-canvas').addClass('hide');
				swal({
					type: 'success',
					title: 'Obrigado por responder o questionário!',
					text: response.html,
					html: true
				}, function(){   
					$('.ajaxLoader').fadeIn();
					window.location = '/portal/dados_saude/dashboard';
				});
			} else {
				$('.app-canvas').html(response.html);	
				return callback(true);
			}
		}
		adjust_canvas();
		view_quest();
	})
	.always(function() {
		$('.ajaxLoader').hide();
	});
	callback(false);
}

function view_quest() {
	$('.respostas').hide();
	$('.respostas').css('visibility', 'visible').fadeIn();
	$('.avancar').fadeIn();
}

function adjust_canvas(){
	$('.vertical-align').height($('.vertical-align').parents('.caixa').outerHeight());
	$('.horizontal-align').width($('.horizontal-align').parents('.caixa').outerWidth());
}

function gerencia_arvore() {
	hoverSrc();
	$('body').on('click', '.open-questions', function(event) {
		var este = $(this);
		var lastNivel = $('tr.questions:last').attr('data-nivel');
		if(este.attr('data-codigo') != '') {
			if(!este.hasClass('open')) {
				este.find('i').removeClass('icon-chevron-right').addClass('icon-chevron-down');
				este.find('div').append(' <img src="'+baseUrl+'img/load-gear.gif" class="loader-gif">');
				este.addClass('open');
				$.ajax({
					url: baseUrl + 'questoes/busca_pergunta',
					type: 'POST',
					dataType: 'json',
					data: {
						codigo: este.attr('data-codigo'),
						padding: este.find('.padding-adjust').css('padding-left').replace('px', ''),
						nivel: este.attr('data-nivel'),
						contaC: este.attr('data-contaC')
					}
				})
				.done(function(response) {
					este.after(response);
					$('.data-src').unbind();
					hoverSrc();
				})
				.always(function() {
					$('.loader-gif').remove();
				});
			} else {
				for (i = parseInt(este.attr('data-nivel'))+1; i <= parseInt(lastNivel); i++) {
					$('tr[data-nivel="'+i+'"]').remove();
				}
				este.find('i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
				este.removeClass('open');
				$('tr[data-nivel="'+ este.attr('data-nivel') +'"]').removeClass('open');
				$('tr[data-nivel="'+ este.attr('data-nivel') +'"]').not('.questions').find('i').removeClass('icon-chevron-down').addClass('icon-chevron-right');
			}
		}
	});
	$('body').on('click', '.js-actions', function(event) {
		event.stopPropagation();
	});
}

function hoverSrc() {
	$('.data-src').hover(function() {
		var id = $(this).attr('data-src');
		$('[data-src="'+id+'"]').find('td').css('background-color', '#ccc');
	}, function() {
		var id = $(this).attr('data-src');
		$('[data-src="'+id+'"]').find('td').removeAttr('style');
	});
}

function alteraResultado(i) {
	$('.js-add-resultado').click(function(event) {
		var memoria = $('.js-memoria').html();
		var contentFieldAfter = 0;
		var input = $('.contentField'+(i-1)).val();
		var labelInput = $('.js-content'+(i-1)).text();
		if(i > 0) {
			var contentFieldAfter = parseInt(input) + 1;
		} 
		if(input == '' || (i > 0 && (isNaN(parseInt(input)) || (parseInt(input) <= parseInt(labelInput))))) {
			swal({
				type: 'warning',
				title: 'Atenção',
				text: 'O valor não pode ser igual ou menor ao valor inicial'
			});
			return false;
		}
		memoria = memoria.replace(/xx/g, i)
		.replace(/disabled="disabled"/g, '')
		.replace('not', '')
		.replace('yy', contentFieldAfter);
		$('.js-entrada').append(memoria);
		i++;
	});
	$('body').on('click', '.js-remover-entrada', function() {

		$(this).parents('.app-begin').remove();
		i--;
		$('.app-begin')
		.not('.not')
		.each(function(index, el) {
			console.log(index);
			nameDescricao = 'data['+index+'][Resultado][descricao]';
			$(this).find('.desc').attr('name', nameDescricao);

			nameValor = 'data['+index+'][Resultado][valor]';
			$(this)
			.find('.val')
			.attr('name', nameValor)
			.removeClass()
			.addClass('input-small val contentField'+index);

			codigoQuestionario = 'data['+index+'][Resultado][codigo_questionario]';
			$(this).find('input:hidden').attr('name', codigoQuestionario);

			$(this)
			.find('.js-valor-anterior')
			.removeClass()
			.addClass('js-valor-anterior js-content'+index);

			if(index > 0) {
				$('.js-content'+index)
				.text( parseInt($('.contentField'+(index-1)).val()) + 1);	
			} else {
				$('.js-content'+index).text('0');
			}
		});
	});
}
