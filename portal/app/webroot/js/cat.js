var cat = new Object();
cat = {
	buscaCEP : function() {
		var erCep = /^\d{5}-\d{3}$/;
		var cepCliente = $.trim($('#CatCepAcidentado').val());
		console.log(cepCliente);
		if(cepCliente != '') {
			cepCliente = cepCliente.replace('-', '');				
			$('#carregando').show();
			
			if(cepCliente.length == 8) {
			    $.ajax({
			        type: 'POST',
			        url: "/portal/enderecos/buscar_endereco_cep/" + cepCliente,
			        dataType: "json",
			        beforeSend: function() { $('#carregando').show(); },
			        success: function(json) {
			        	console.log(json);
			    		if(json.VEndereco) {
			    			
							cat.completaEndereco('cat', json.VEndereco.endereco_tipo + ' ' +  json.VEndereco.endereco_logradouro, json.VEndereco.endereco_bairro, json.VEndereco.endereco_codigo_cidade,json.VEndereco.endereco_cidade, json.VEndereco.endereco_estado);
							$('#carregando').hide();
			    		} else {
			    			
			    			$('#carregando').hide();
			    			alert('Cep não encontrado, digite na mão!');
			    		}
			        },
			        complete: function() { $('#carregando').hide(); }
			    });
			} else if(cepCliente.length > 0) {
				alert('cep inválido');
			}			
		}
	},
	buscaCidade : function(idEstado, idCidade) {
		$.ajax({
	        type: 'POST',
	        url: "/portal/enderecos/carrega_combo_cidade/" + idEstado,
	        dataType: "html",
	        beforeSend: function() {
	        	$('#cidade_combo').hide();
	        	$('#carregando_cidade').show();
	        },
	        success: function(retorno) {
	        	$('#catCodigoCidade').html(retorno);
	        	
	        	if(idCidade)
	        		$('select[name="data[cat][codigo_cidade]"]').val( idCidade );
	        },
	        complete: function() {
	        	$('#carregando_cidade').hide();
	        	$('#cidade_combo').show();
	        }
	    });
	},	
	completaEndereco : function (Model, logradouro, bairro, cod_cidade, desc_cidade, estado) {
		idEstado = $('select[name="data[' + Model + '][acidentado_estado]"] option').filter(function () { return $(this).html() == estado; }).val();
		$('#CatAcidenteEstado').val(estado);
		$('#CatAcidentadoEndereco').val(logradouro);
		$('#CatAcidentadoCidade').val(desc_cidade);
		$('#CatAcidentadoBairro').val(bairro);

		cat.buscaCidade(idEstado, cod_cidade);
	}
}
		
$(document).ready(function() {
	setup_datepicker();
	setup_time();

	$('#CatCodigoCaepf').hide();
	$('#CatCodigoCno').hide();
	$('#caepf_title').hide();
	$('#cno_title').hide();

	$('#CatTipoInscricao').on('change', function() {
      	if ( this.value == '3') {
      		$('#caepf_title').show();
      		$('#CatCodigoCaepf').show();
      		$('#CatCodigoCno').hide();
      		$('#CatCodigoCno').val('');
      		$('#cno_title').hide();
      	} else if (this.value == '4'){
        	$('#cno_title').show();
        	$('#CatCodigoCno').show();
        	$('#CatCodigoCaepf').hide();
        	$('#caepf_title').hide();
        	$('#CatCodigoCaepf').val('');
      	} else if (this.value == '1'){
        	$('#CatCodigoCno').hide();
        	$('#CatCodigoCaepf').hide();
        	$('#CatCodigoCno').val('');
        	$('#CatCodigoCaepf').val('');
        	$('#caepf_title').hide();
			$('#cno_title').hide();
      	}
	});

	$('.datepickerjs').datepicker({
		dateFormat: 'dd/mm/yy',
		showOn : 'button',
		buttonImage : baseUrl + 'img/calendar.gif',
		buttonImageOnly : true,
		buttonText : 'Escolha uma data',
		dayNames : ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sabado'],
		dayNamesShort : ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
		dayNamesMin : ['D','S','T','Q','Q','S','S'],
		monthNames : ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		monthNamesShort : ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],           
		onClose : function() {
		}
	}).mask('99/99/9999'); 

	$('.js-example-basic-single').select2();

	$('#emissao_cpf').hide();

	$('#CatMotivoEmissao').change(function(){
		$('#emissao_cpf').hide();
		//verifica se é Iniciativa
		if($(this).val() == '1') {
			$('#emissao_cpf').show();
		}
	});

		var i = 1;
	$('body').on('click', '.js-add-cid', function() {
		var html = $(this).parents('.js-encapsulado').find('.js-memory').html().replace(/xx/g, i).replace(/Xx/g, i).replace(/disabled="disabled"/g, '');
		$(this).parents('.js-encapsulado').append(html).find('.inputs-config.hide').show();
		$(this).removeClass('js-add-cid').addClass('js-remove-cid').attr('data-original-title', 'Remover doença').children('i').removeClass('icon-plus').addClass('icon-minus');
		$('[data-toggle="tooltip"]').tooltip();
		i++;
	});//FINAL CLICK js-add-cid
	
	$('body').on('click', '.js-remove-cid', function() {
		$(this).parents('.inputs-config').remove();
	});//FINAL CLICK js-remove-cid

	// modulo CID
	var timer;
	$("body").on('keyup', '.js-cid-10, .js-cid10', function() {
		var este = $(this);
		var string = this.value;
		if(string != '') {
			este.parent().css('position', 'relative');
			$('.loader-gif').remove();
			este.parent().append(' <img src="'+baseUrl+'img/default.gif" style="margin-top: -10px;" class="loader-gif">');
			$('.seleciona-cid-10').remove();
			clearTimeout(timer); 
			timer = setTimeout(function() {
				$.ajax({
					url: baseUrl + 'cid/carregaCidsParaAjax/',
					type: 'POST',
					dataType: 'json',
					data: {string: string},
				})
				.done(function(response) {
					if(response) {
						var canvas = $('<div>', {class: 'seleciona-cid-10'}).html(response);
						este.parent().append(canvas);
					}
				})
				.always(function() {
					$('.loader-gif').remove();
				});
			}, 500);
		} else {
			$('.seleciona-cid-10').remove();
			$('.loader-gif').remove();
		}
	});//FINAL keyup CLASSE js-cid-10 E js-cid10

	$('body').on('click', '.js-cid-click', function() {
		$(this).closest('.checkbox-canvas').find('.js-cid10').val($(this).find('td:first-child').text());
		$(this).parents('.checkbox-canvas').find('.js-cid-10').val($(this).find('td:first-child').text());
		$('.seleciona-cid-10').remove();
	});//FINAL click CLASSE js-cid-click

	$('body').click(function(event) {
		$('.seleciona-cid-10').remove();
	});
	// ===============
});

jQuery('#CatCodigoEsocial13').change(function(){
	if($('#CatCodigoEsocial13').val() == ''){
		$('#hidee').hide();
		$('#CatLateralidadeCorpo').val('');
	} else {
		$('#hidee').show();
	}
});	