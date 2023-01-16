var atestado = new Object();
atestado = {
	buscaCEP : function() {
		var erCep = /^\d{5}-\d{3}$/;
		var cepCliente = $.trim($('#AtestadoCep').val());
		
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
			    		if(json.VEndereco) {
			    			
							atestado.completaEndereco('Atestado', json.VEndereco.endereco_tipo + ' ' +  json.VEndereco.endereco_logradouro, json.VEndereco.endereco_bairro, json.VEndereco.endereco_codigo_cidade, null, json.VEndereco.endereco_estado);
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
	        	$('#AtestadoCodigoCidade').html(retorno);
	        	
	        	if(idCidade)
	        		$('select[name="data[Atestado][codigo_cidade]"]').val( idCidade );
	        },
	        complete: function() {
	        	$('#carregando_cidade').hide();
	        	$('#cidade_combo').show();
	        }
	    });
	},	
	completaEndereco : function (Model, logradouro, bairro, cod_cidade, desc_cidade, estado) {
		idEstado = $('select[name="data[' + Model + '][codigo_estado]"] option').filter(function () { return $(this).html() == estado; }).val();
		$('select[name="data[' + Model + '][codigo_estado]"]').val(idEstado);
		$('input[name="data[' + Model + '][endereco]"]').val(logradouro);
		$('input[name="data[' + Model + '][bairro]"]').val(bairro);

		atestado.buscaCidade(idEstado, cod_cidade);
	}
}
		
$(document).ready(function() {

	    saveAtestados = function() {

        var retorno = true;	
		

        $("#AtestadoCodigoMotivoLicenca").each(function(indice){        	
        	if(this.value == 'Selecione'){
        		swal({
					type: 'warning',
					title: 'Atenção',
					text: 'O campo Motivo da Licença é obrigatório.'
				});
				$('#AtestadoCodigoMotivoLicenca').focus();
				retorno = false;
        	}
        });

        $(".motivo_18").each(function(indice){
			var observacao = $('#AtestadoObsAfastamento').val();
        	if($(this).val() == 1034 ) {// se for selecionado o motivo 21 - Licença não remunerada ou sem vencimento              		
        		if(observacao.trim() == ''){        		        			
					swal({
						type: 'warning',
						title: 'Atenção',
						text: 'O campo observação é obrigatório.'
					});
					$('#AtestadoObsAfastamento').focus();
        			retorno = false;		        			
        		}
        	}		
		});


		$("#AtestadoOrigemRetificacao").each(function(indice){			
			var numero_processo = $('#AtestadoNumeroProcesso').val();			
			var tipo_processo = $('#AtestadoTipoProcesso').val();

			if(this.value != '' && this.value == 2 || this.value == 3){										
				if(numero_processo.trim() == ''){
					swal({
						type: 'warning',
						title: 'Atenção',
						text: 'O campo Numero Processo é obrigatório.'
					});
					$('#AtestadoNumeroProcesso').focus();
	                retorno = false;		        						
				} else if(tipo_processo.trim() == ''){
					$('#AtestadoTipoProcesso').focus();	
					swal({
						type: 'warning',
						title: 'Atenção',
						text: 'O campo Tipo de processo é obrigatório.'
					});	
					retorno = false;		
				}
			}
		});
		
		var editando = $('#AtestadoEditando').val();
		
		if(editando == 1){//validar somente quando for edição
			$("#AtestadoTipoProcesso").each(function(indice){
				var numero_processo = $('#AtestadoNumeroProcesso').val();//numero processo
				numero_processo.replace(/\s/g,'');				

				if(this.value != '' && this.value == 1){					
					if(numero_processo.length != 17 && numero_processo.length != 21){									
						$('#AtestadoNumeroProcesso').focus();
						swal({
							type: 'warning',
							title: 'Atenção',
							text: 'O campo Número Processo deve ser preenchido exatamente com 17 ou 21 algarismos.'
						});
						retorno = false;	
					}
				} else if(this.value != '' && this.value == 2){
					if(numero_processo.length != 20){					
						$('#AtestadoNumeroProcesso').focus();
						swal({
							type: 'warning',
							title: 'Atenção',
							text: 'O campo Número Processo deve ser preenchido exatamente com 20 algarismos.'
						});
						retorno = false;		
					}
				} else if(this.value != '' && this.value == 3){
					if(numero_processo.length != 10){				
						$('#AtestadoNumeroProcesso').focus();
						swal({
							type: 'warning',
							title: 'Atenção',
							text: 'O campo Número Processo deve ser preenchido exatamente com 10 algarismos.'
						});
						retorno = false;		
					}
				}
			});	
		}

        $(".acidente_transito").each(function(indice){ 
        	var id = $(this).prop('id');		
			if($('#'+id).prop('checked')) {        		
        		if(this.value == 1){        			
        			$("#AtestadoTipoAcidenteTransito").each(function(indice){												
						if(this.value == ''){
							$('#AtestadoTipoAcidenteTransito').focus();	
							swal({
								type: 'warning',
								title: 'Atenção',
								text: 'O campo Tipo de acidente de trânsito é obrigatório.'
							});
							retorno = false;
						}
					});
        		}       	
			}
        });

        $(".motivo_18").each(function(indice){
        	if($(this).val() == 1028 ) {
        		if($('#AtestadoDataInicioPAquisitivo').val() == ''){
        			swal('Erro!', 'O campo Data Início Período Aquisitivo é obrigatório :)', 'error');
					jQuery("input[name='data[Atestado][data_inicio_p_aquisitivo]']").css('box-shadow','0 0 5px 1px red');
					retorno = false;
        		}
        	}
        });

        if($('#AtestadoDataFimPAquisitivo').val() != ''){
			if(!data_fim_menor_inicio_aquisitivo($('#AtestadoDataFimPAquisitivo').val(), $('#AtestadoDataInicioPAquisitivo').val())) {
				retorno = false;						
			} 	
        }

        $("#AtestadoSemProfissional").each(function(indice){
        	var id = $(this).prop('id');
        	var codigo_medico_pcmso = $('#AtestadoCodigoMedico').val();

        	if($('#'+id).prop('checked')) {
        	} else {
        		if(codigo_medico_pcmso == ''){
        			swal('Erro!', 'O campo Coord PCMSO é obrigatório :)', 'error');
					jQuery("input[name='data[Atestado][codigo_medico]']").css('box-shadow','0 0 5px 1px red');
					retorno = false;
        		}	
        	}
        });
		// alert(retorno); 
		// exit;

        if(retorno == true){

            if ( $("#AtestadoEditarForm").length ) {
                $("#AtestadoEditarForm").submit();
            } else {
                $("#AtestadoIncluirForm").submit();
            }
        }

        return
    }   
});

function data_fim_menor_inicio_aquisitivo(data_fim, data_inicio) {	
	if(data_fim != ''){
		data_fim = data_fim.split('/');
		data_inicio = data_inicio.split('/');

		if(parseInt(data_fim[2] + data_fim[1] + data_fim[0]) <= parseInt(data_inicio[2] + data_inicio[1] + data_inicio[0])) {

			jQuery("input[name='data[data_fim_p_aquisitivo]']").css('box-shadow','0 0 5px 1px red');
			
			swal({
				type: 'warning',
				title: 'Atenção',
				text: 'Data Fim Período Aquisitivo deve ser maior à Data Início Período Aquisitivo.',
			});
			return false;
		} else {
			return true;
		}
	}
}


function conflitoAtestado(){

	var codigoClienteFunc = $('#AtestadoCodigoClienteFuncionario').val();
	var data_inicial_pt = $('#AtestadoDataAfastamentoPeriodo').val();		
	var data_final_pt = $('#AtestadoDataRetornoPeriodo').val();
	var data_inicial = FormataStringData($('#AtestadoDataAfastamentoPeriodo').val());		
	var data_final = FormataStringData($('#AtestadoDataRetornoPeriodo').val());
	var hora_inicio = $('#AtestadoHoraAfastamento').val();
	var hora_fim = $('#AtestadoHoraRetorno').val();
	
		if(data_inicial_pt || data_final_pt || hora_inicio || hora_fim){
		
			$.ajax({
				url: baseUrl + "atestados/busca_conflito_atestado/" + codigoClienteFunc + "/" + data_inicial + "/" + data_final+ "/" + hora_inicio+ "/" + hora_fim,
				dataType: "json",					
				success: function(data) {
					if(data.return == 1) {	                		
						swal({
							type: 'warning',
							title: 'Atenção',
							text: 'Já Existe um cadastro para esse período: '+ data_inicial_pt +" á " + data_final_pt + '\r\n\r\n Gostaria de continuar com o cadastro?'
						});	
					} else {    		
						// alert("Não!");
						
					}               
				},
				complete: function(data){	                	
				}
			});	

		}
		
		function FormataStringData(data) {
			var dia  = data.split("/")[0];
			var mes  = data.split("/")[1];
			var ano  = data.split("/")[2];
		  
			return ano + '-' + ("0"+mes).slice(-2) + '-' + ("0"+dia).slice(-2);
			// Utilizo o .slice(-2) para garantir o formato com 2 digitos.
		}
	
}