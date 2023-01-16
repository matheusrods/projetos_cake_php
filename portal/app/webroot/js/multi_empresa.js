var multiempresa = new Object();
multiempresa = {
	validaCNPJ : function(element) {
		var cnpj = $(element).val().replace(/[^0-9]/g,'');
		
		if(cnpj.length == 14) {
		    $.ajax({
		        type: 'POST',
		        url: "/portal/multi_empresas/verifica_cnpj",
		        data: "cnpj=" + cnpj,
		        dataType: "json",
		        beforeSend: function() {
		    		$('#cnpj_loading').fadeIn();
		    		$('#link_auto_completar_cnpj').hide();
		    	},
		        success: function(json) {
		    		// ja esta cadastrado na base?
		    		if(json.resultado) {
		    			
	    				$('#modal').modal('show');
	    				$('#modal .modal-header').html('<b>CNPJ JÁ CADASTRADO</b>');
	    				$('#modal .modal-body').html('Este CNPJ já esta cadastrado em nosso sistema, não é permitido cadastro duplicado! <br /><br /> <a href="javascript:void(0);" class="btn btn-danger right" onclick="$(\'#modal\').modal(\'hide\');" style="margin-right: 5px;"><i class="icon-white icon-remove-sign"></i> Fechar</a><div class="clear"></div>');
	    				
	    				$(element).val('');
		    			
		    		} else {
		    			if(json.valido) {
		    				$('#link_auto_completar_cnpj').fadeIn();
		    			} else {
		    				$('#modal_BO').modal('show');
		    				$('#modal_BO #msg_error').html('CNPJ Inválido');
		    				$('#botao_fechar').remove();
		    				$('#modal_BO .modal-body').append('<a href="javascript:void(0);" class="btn btn-success" onclick="$(\'#modal_BO\').modal(\'hide\'); $(\'#modal\').modal(\'hide\');" id="botao_fechar"><i class="icon-white icon-ok-sign"></i> Tentar Novamente </a>');		    				
		    			}
		    		}	    		
		    	},
		        complete: function() {
		    		$('#cnpj_loading').hide();
		    	}
		    });
		}
	},		
	carregaCNPJ : function() {
		$('#modal').modal('hide');
		$('#modal_receita input[name="data[texto_captcha]"]').val('');
		$('#modal_receita').modal('show');
	},
	trocaCaptcha : function() {
		$('#troca_imagem').hide();
		$('#img_captcha').remove();
		$('#carregando_captcha').show();
		
		$('#modal_receita .modal-body').prepend('<img border="0" id="img_captcha" src="/portal/multi_empresas/getcaptcha?' + Math.random() + '">');
		
		$('#img_captcha').one('load',function() {
			$('#carregando_captcha').hide();
			$('#troca_imagem').show();
			$('input[name="data[texto_captcha]"]').val('');
	    });
	},
	enviaCaptcha : function (element, contador, etapa) {
		
		var cnpj = $('input[name="data[MultiEmpresa][codigo_documento]"]').val().replace(/[^0-9]/g,'');
		var captcha = $('input[name="data[texto_captcha]"]').val();
		
	    $.ajax({
	        type: 'POST',
	        url: "/portal/multi_empresas/retorno_receita",
	        data: "cnpj=" + cnpj + "&captcha=" + captcha,
	        dataType: "json",
	        beforeSend: function() {
	    		$('#carregando_receita').show();
	    	},
	        success: function(json) {
	        	
	    		if($.trim(json.status) == 'OK') {
	    			var modelPrimaria = 'MultiEmpresa';
	    			var modelEndereco = 'MultiEmpresaEndereco';
	    			
	    			multiempresa.completaEndereco(modelEndereco, json[7], json[11], null, json[12], json[13], json[8], json[10], json[2], json[3], json[14], modelPrimaria);
	    			
	    			$('#modal_receita').modal('hide');
	    			$('#carregando_receita').hide();
	    		} else {
	    			if(contador == 0) {
	    				$('#modal_receita').modal('show');
	    				multiempresa.enviaCaptcha(element, 1, etapa);
	    				
	    			} else {
			    		$('#modal_receita').modal('hide');
			    		$('#carregando_receita').hide();
			    		
			    		$('#msg_error').html(json.status);
			    		$('#modal_BO').modal('show');
			    		
			    		$('#botao_fechar').remove();
			    		$('#modal_BO .modal-body').append('<a href="javascript:void(0);" class="btn btn-success" onclick="multiempresa.tentarNovoCaptcha();" id="botao_fechar"><i class="icon-white icon-ok-sign"></i> Tentar Novamente </a>')			    		
	    			}
	    		}
	    	},
	    	complete: function() {
	    		if(contador == 1) {
		    		$('#modal_receita').modal('hide');
		    		$('#carregando_receita').hide();	    			
	    		}
	    	}
	    });
		
	},
	tentarNovoCaptcha : function() {
		$('#modal').modal('hide');
		$('#modal_BO').modal('hide');
		
		multiempresa.limpaCookies();
		multiempresa.trocaCaptcha();
		
		$('#troca_imagem').show();
		$('#modal_receita').modal('show');
	},
	limpaCookies : function() {
		$.post('/portal/multi_empresas/limpa_cookie');
	},
	buscaCEP : function(modelEndereco, ModelPrincipal) {
		var erCep = /^\d{5}-\d{3}$/;
		var cepCliente = $.trim($('#' + modelEndereco + 'Cep').val());
		
		if(cepCliente != '') {
			cepCliente = cepCliente.replace('-', '');				
			$('#carregando').show();
			
			if(cepCliente.length == 8) {
			    $.ajax({
			        type: 'POST',
			        url: "/portal/enderecos/buscar_endereco_cep/" + cepCliente,
			        dataType: "json",
			        beforeSend: function() { $('#pesquisa_cep').hide(); $('#carregando').show(); },
			        success: function(json) {
			    		if(json.VEndereco) {
							multiempresa.completaEndereco(modelEndereco, json.VEndereco.endereco_tipo + ' ' +  json.VEndereco.endereco_logradouro, json.VEndereco.endereco_bairro, json.VEndereco.endereco_codigo_cidade, null, json.VEndereco.endereco_estado, null, null, null, null, null, ModelPrincipal);
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
	buscaCidade : function(estado, idEstado, idCombo, desc_cidade, cod_cidade) {

		if(!idEstado) {
			idEstado = $(estado).val();
		}
		
		$.ajax({
	        type: 'POST',
	        url: "/portal/enderecos/carrega_combo_cidade/" + idEstado,
	        dataType: "html",
	        beforeSend: function() { 
	        	$('#cidade_combo').hide();
	        	$('#carregando_cidade').show();
	        },
	        success: function(retorno) {
	        	$('#' + idCombo).html(retorno);

	        	if(desc_cidade) {
	        		$('select[name="data[MultiEmpresaEndereco][codigo_cidade_endereco]"]').val($('select[name="data[MultiEmpresaEndereco][codigo_cidade_endereco]"] option').filter(function () { return $(this).html().toUpperCase() == desc_cidade; }).val());
	        	} else {
	        		$('select[name="data[MultiEmpresaEndereco][codigo_cidade_endereco]"]').val( cod_cidade );
	        	}
	        },
	        complete: function() { 
	        	$('#carregando_cidade').hide();
	        	$('#cidade_combo').show();
	        }
	    });
	},	
	completaEndereco : function (ModelEndereco, logradouro, bairro, cod_cidade, desc_cidade, estado, numero, cep, razao_social, nome_fantasia, email, ModelPrincipal) {
		
		idEstado = $('select[name="data[' + ModelEndereco + '][codigo_estado_endereco]"] option').filter(function () { return $(this).html() == estado; }).val();
		
		if(estado != '********')
			$('select[name="data[' + ModelEndereco + '][codigo_estado_endereco]"]').val(idEstado);
		
		if(logradouro != '********')
			$('input[name="data[' + ModelEndereco + '][logradouro]"]').val(logradouro);
		
		if(bairro != '********')
			$('input[name="data[' + ModelEndereco + '][bairro]"]').val(bairro);

		var idComboCidade = 'MultiEmpresaEnderecoCodigoCidadeEndereco';
		multiempresa.buscaCidade(null, idEstado, idComboCidade, desc_cidade, cod_cidade);
		
		if((cep != null) && (cep != '********'))
			$('input[name="data[' + ModelEndereco + '][cep]"]').val(cep);
		
		if((razao_social != null) && (razao_social != '********'))
			$('input[name="data[' + ModelPrincipal + '][razao_social]"]').val(razao_social);
		
		if((nome_fantasia != null) && (nome_fantasia != '********'))
			$('input[name="data[' + ModelPrincipal + '][nome_fantasia]"]').val(nome_fantasia);
		
		if((numero != null) && (numero != '********'))
			$('input[name="data[' + ModelEndereco + '][numero]"]').val(numero);
		
	}
}
		
$(document).ready(function() {
	
});