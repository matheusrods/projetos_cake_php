jQuery(document).ready(function() {
	
	jQuery(document).on("click","#carregar-loadplan",function(){

		if($('input.load-field ').length > 0){
			var ja_informado = false;
			$('input.load-field ').each(function(){
				if($( this ).val() == $( '#LogIntegracaoCodigo' ).val()){
					alert('O numero de LOADPLAN já foi digitado.');
					ja_informado = true;
					return false;
				}
			});
			
			if(ja_informado) return false;
		}
		
		//checkar_loadplan("#LogIntegracaoCodigo","#RecebsmCodigoAlvosTra","div#loadplan",1);
		carregar_loadplan("#LogIntegracaoCodigo","#RecebsmCodigoAlvosTra","div#loadplan",1);
		//checkar_loadplan(element_loadplan,element_transportador,conteiner,tparada)
		return false;
	});

	jQuery(document).on("click","#carregar-parada",function(){
		carregar_loadplan("#LogIntegracaoCodigo","#RecebsmCodigoAlvosTra","div#loadplan",0);
		return false;
	});

	jQuery(document).on('keydown',function(event){
		if(event.which == 13) return false;
	})

	jQuery(document).on("click",".remove-loadplan",function(){
		$(this).parents('div.erro:eq(0)').remove();
		return false;
	});	

	jQuery(document).on("click",".add-modelo",function(){
		jQuery("#modelo").slideDown('fast');
		jQuery("#modelo input").focus();
		return false;
	});

	jQuery(document).on("click",".dele-modelo",function(){
		if(confirm("Deseja remover este modelo?")){
			var excluir = jQuery(this);
			jQuery.ajax({
				url: baseUrl + "ModelosViagens/excluir/" + excluir.attr("mviacodigo") + "/" + Math.random(),
				dataType: "json",
				success: function(data){
					if(!data) alert("Falha ao remover modelo");
				},
				complete: function(){
					atualizar_modelos(jQuery("#RecebsmCodigoCliente").val(),"#lista ul");
				}
			})
		}

		return false;
	});

	jQuery(document).on("change","#RecebsmCodigoUsuario",function(){
		
		if($(this).val()){
			atualizar_modelos(jQuery("#RecebsmCodigoCliente").val(),"#lista ul");
		} else {
			$("#lista ul").parents("div:eq(0)").css('display','none');
		}
	});

	jQuery(document).on("click",".load-modelo",function(){
		var remonta = jQuery('#RecebsmConsultarParaIncluirRemontaForm').length == 1 ? '1' : '0';
		var conteudo = jQuery("#pre-modelo");
		var codigo_cliente = jQuery("#RecebsmCodigoCliente").val();
		jQuery.ajax({
			url: baseUrl + "ModelosViagens/carregar_pre_cadastro/" + jQuery(this).attr("mviacodigo") + "/" + codigo_cliente + "/" + remonta+ "/" + Math.random(),
			dataType: "html",
			beforeSend: function(){
				bloquearDiv(conteudo);
			},
			success: function(data){
				conteudo.html(data);
			},
			complete: function(){
				setup_mascaras();
				setup_datepicker();
				$.placeholder.shim();
				jQuery(".blockUI").remove();
			}
		})
	});

	function atualizar_modelos(codigo_cliente,element_conteiner){
		var conteiner  = jQuery(element_conteiner);

		jQuery.ajax({
			url: baseUrl + "ModelosViagens/listar_modelos/" + codigo_cliente + "/" + Math.random(),
			dataType: "html",
			beforeSend: function(){
				conteiner.parents("div:eq(0)").css('display','block');
				bloquearDiv(conteiner);
			},
			success: function(data){
				conteiner.html(data);
				if(!data){
					conteiner.parents("div:eq(0)").css('display','none');
				}
			},
			complete: function(){
				jQuery(".blockUI").remove();
			}
		});

		return false;
	};

	jQuery(document).on("keydown","#TMviaModeloViagemMviaDescricao",function(e) {
		if(e.which == 13) return false;
	});

	jQuery(document).on("click","#cancelar-modelo",function(){
		cancelar();
		return false;
	});

	jQuery(document).on("click","#salvar-modelo",function(){
		var dados 	= jQuery("form").serialize();
		var erro 	= jQuery("#modelo-erro");
		var sucesso	= jQuery("#modelo-sucesso");
		var form 	= jQuery("#modelo-form");

		if(!jQuery("#TMviaModeloViagemMviaDescricao").val()){
			alert("Informe uma descrição");
			jQuery("#TMviaModeloViagemMviaDescricao").focus();
			return false;
		}

		jQuery.ajax({
			type:"post",
			url: baseUrl + "ModelosViagens/adicionar/" + Math.random(),
			dataType: "json",
			data: dados,
			beforeSend: function(){
				bloquearDiv(jQuery("#modelo"));
			},
			success: function(data){
				if(!data.sucesso){
					erro.html('Erro!');
					if(data.msg)erro.html(data.msg);
					
					erro.fadeIn('very fast');
				} else {
					sucesso.fadeIn('very fast');
				}
			},
			error: function(){
				erro.fadeIn('very fast');
			},
			complete: function(){
				form.hide();
				jQuery('.blockUI').remove();
				setTimeout(function(){
			       cancelar();
			    }, 1500);
			}
		})

		return false;
	});

	cancelar();
	function cancelar(){
		jQuery("div#modelo").fadeOut("fast",function(){
			jQuery("div#modelo input").val("");
			jQuery("#modelo-erro").hide();
			jQuery("#modelo-sucesso").hide();
			jQuery("#modelo-form").show();
		});		
	}

	jQuery("BODY").scrollspy({
	  offset: 80
	});
	var $window = jQuery(window);
	// side bar
	jQuery(".bs-docs-sidenav").affix({
	  offset: {
		top: function () { return $window.width() <= 980 ? 290 : 50 }
	  }
	});

});

function mascara_cpf_motorista(documento,estrangeiro){
	documento.val(null);
	documento.removeClass("formata-doc");
	documento.removeClass("formata-rne");
	documento.removeClass("formata-cpf");

	if(estrangeiro)
		documento.addClass("formata-rne");
	else
		documento.addClass("formata-cpf");

	setup_mascaras();
}

function carregar_sm_itinerario(codigo,element_conteiner, remonta,codigo_cliente){
	var conteudo = jQuery(element_conteiner);
	jQuery.ajax({
		url: baseUrl + "ModelosViagens/carregar/" + codigo + "/" + remonta + "/" + codigo_cliente + "/" + Math.random(),
		dataType: "html",
		beforeSend: function(){
			bloquearDiv(conteudo);
		},
		success: function(data){
			conteudo.html(data);
		},
		complete: function(){
			conteudo.attr('style', '');
			setup_datepicker();
			$.placeholder.shim();
		}
	})
};

function checkar_loadplan(element_loadplan,element_transportador,conteiner,tparada){
	conteiner 	= $(conteiner);
	var dados 	= {
		data: {
			loadplan: $(element_loadplan).val()
		}
	};

	$.ajax({
		type: 'post',
		url: baseUrl + "SolicitacoesMonitoramento/checkar_loadplan/" + Math.random(),
		data: dados,
		beforeSend: function(){
			$(element_loadplan).addClass('ui-autocomplete-loading');
		},
		success: function(data){
			if(data == '1') {
				if(confirm('LOADPLAN já utilizado, deseja continuar mesmo assim?')){
					carregar_loadplan(element_loadplan,element_transportador,conteiner,tparada);
				}
			} else {
				carregar_loadplan(element_loadplan,element_transportador,conteiner,tparada);
			}
			
		},
		complete: function(){
			$(element_loadplan).removeClass('ui-autocomplete-loading');
		}
	});
	
}

function carregar_loadplan(element_loadplan,element_transportador,conteiner,tparada){
	conteiner = $(conteiner);
	var maxId = -1;
	
	conteiner.find('table.destino').each(function(){
		if(maxId < parseInt($(this).attr('max-id')))
			maxId = parseInt($(this).attr('max-id'));
	});

	if(tparada == "undefined")
		tparada = 1;

	if(tparada == 0 && maxId < 0){
		alert("informa um LOADPLAN antes de incluir uma parada.");
		return false;
	}


	var dados 	 = {
		data: {
			loadplan: $(element_loadplan).val(),
			transportador: $(element_transportador).val(),
			key: maxId+1,
			parada: tparada 
		}
	};

	$.ajax({
		type: 'post',
		url: baseUrl + "LogsIntegracoes/carregar_loadplan_row/" + Math.random(),
		data: dados,
		beforeSend: function(){
			bloquearDiv($(conteiner));
		},
		success: function(data){
			if(data)conteiner.append(data);
		},
		complete: function(){
			$(element_loadplan).val(null);
			$('.blockUI').remove();
			setup_datepicker();
			setup_time();
			setup_mascaras();
			$.placeholder.shim();
		}
	});

}