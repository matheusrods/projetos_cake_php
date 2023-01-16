<?php echo $this->BForm->success_menssage($sm) ?>
<div class="span12" style="margin-left:0;">
	<?php echo $this->BForm->create('Recebsm', array('url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'incluir_facilitada')), '');?>
		<?php if($mensagem): ?>
		<section class="form-actions alert-error veiculo-error" >
			<h5>Erros:</h5>
			<?php echo $mensagem ?>
		</section>
		<?php endif; ?>

		<?php if(!$authUsuario['Usuario']['codigo_cliente']):?>
			<h4>Cliente</h4>
		<?php elseif($authUsuario['Usuario']['codigo_cliente'] && $fields_view):?>
			<div class="control-group input select error">
			<h4 class="help-block error-message">Cliente</h4>
			<div class="help-block error-message">Usuario não habilitado para a inclusão de SM's</div>
			</div>
		<?php endif ?>

		<div class='row-fluid inline'>
			<?php echo $this->Buonny->input_cliente_usuario_cliente_monitora($this, $usuarios) ?>
			<?php echo $this->BForm->hidden('codigo_cliente2') ?>
		</div>

		<div id="fields-view" style="<?php echo $fields_view ?>">

			<div id="pre-modelo">
				<?php echo $this->element('solicitacoes_monitoramento/incluir_facilitada') ?>
			</div>

			<div class="form-actions">
				<?php echo $this->BForm->submit('Gerar SM', array('div' => false, 'class' => 'btn btn-primary')); ?>
				<?php echo $this->Html->link('Cancelar', array('controller' => 'solicitacoes_monitoramento', 'action' => 'incluir_facilitada'), array('class' => 'btn')); ?>
			</div>
		</div>

	<?php echo $this->BForm->end(); ?>
</div>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Javascript->codeBlock("
    var contador_destino = $('div.destino table.destino').length;
	var consultando = false;

	function carregar_produtos(transportador,embarcador){
		$.ajax({
	        'url': baseUrl + 'solicitacoes_monitoramento/lista_produtos_express/' + transportador + '/' + embarcador + '/' + Math.random(),
	        dataType: 'json',
	        beforeSend: function(){
	        	bloquearDiv($('#itinerario'));
	        },
	        'success': function(data) {	       
	            $('.carga-produtos').html(data.html);
	        },
	        complete: function(){
	        	$('#itinerario').unblock();
	        },
		});
		return true;
	}

	function mostraFaixaTemperatura() {
		var selecao = $('#RecebsmEscolhaTemperatura').val();
		if (selecao == 1) {
			$('div#temperatura').show();
		} else {
			$('div#temperatura').hide();
		}
	}

	function consulta_codigo_externo_veiculo() {
		var codigo_externo = $('#RecebsmVeicCodigoExterno').val();
		var codigo_cliente = $('#RecebsmCodigoCliente').val();

		$('.help-block').remove();
		$('#RecebsmVeicCodigoExterno').removeClass('form-error');
		$('#RecebsmVeicCodigoExterno').parents().find('.error').removeClass('error');			

		if (codigo_externo=='') {
			$('#RecebsmPlaca').val('');
			return false;
		}
		if (codigo_cliente=='') {
			$('#RecebsmPlaca').val('');
			return false;
		}
		$.ajax({
			url: baseUrl + 'solicitacoes_monitoramento/retorna_placa_por_codigo_externo/' + codigo_cliente + '/' + codigo_externo + '/' + Math.random(),
			dataType: 'json',
			beforeSend: function(){
				$('.help-block').remove();
				$('#RecebsmVeicCodigoExterno').removeClass('form-error');
				$('#RecebsmVeicCodigoExterno').parents().find('.error').removeClass('error');	
                $('#RecebsmVeicCodigoExterno').addClass('ui-autocomplete-loading');
			},					
			success: function(data){
				$('#RecebsmLoading').hide();
				if(data){
					$('#RecebsmPlaca').val(data.veic_placa);
					consulta_tipo_placa_express($(document),$('#RecebsmCodigoCliente'));
				} else {
					$('#RecebsmVeicCodigoExterno').addClass('form-error').parent().addClass('error').append('<div id=\'lbl-error\' class=\'help-block\'>Código não encontrado</div>');
				}
			},
			complete: function(){
                $('#RecebsmVeicCodigoExterno').removeClass('ui-autocomplete-loading');
            }
		});
	}
	

	$(document).ready(function () {
		setup_time();
		setup_datepicker();
		mostraFaixaTemperatura();	
		//caso caia no invalidate já estara carregado o codigo transportador
		if($('#RecebsmTransportador').val() != ''){
			carregar_produtos($('#RecebsmTransportador').val(),$('#RecebsmEmbarcador').val());
		}
		$(document).on('change','#RecebsmEscolhaTemperatura',function(){
	  		mostraFaixaTemperatura();
		});
		
		$(document).on('change','#RecebsmCodigoUsuario',function(){
			if($(this).val()){
				$('#fields-view').slideDown();
				bloquearDiv($('#fields-view'));
				embarcador_transportador();				
				carregar_janelas(false, false);
				
				carregar_produtos($('#RecebsmCodigoCliente').val(),$('#RecebsmCodigoCliente').val());				
				".(!empty($alvo_origem_padrao) ? 'carregar_alvos_destino(false);' :"")."
				$.placeholder.shim();
			} else {
				$('#fields-view').slideUp();
				
			}
		});
		

		$(document).on('change','#RecebsmVeicCodigoExterno',function(){
			consulta_codigo_externo_veiculo();
		});

		$(document).on('change','#RecebsmTransportador, #RecebsmEmbarcador',function(){
			$('#RecebsmTransportador').parent().removeClass('error').find('.error-message').remove();
			if($(this).val() != $('#RecebsmCodigoCliente').val()){
				$('#RecebsmCodigoCliente2').val($(this).val());
			}
			if($('#RecebsmTransportador').val() && $('#RecebsmEmbarcador').val()){
				transportador = $('#RecebsmTransportador').val();
				embarcador = $('#RecebsmEmbarcador').val();
				bloquearDiv($('.emba_tran'));
				$.ajax({
			        'url': baseUrl + 'solicitacoes_monitoramento/verificar_cliente_pagador/' + embarcador + '/'+ transportador +'/' + Math.random(),
			        dataType: 'json',
			        'success': function(data) {	
			       	if(data){
			            if(data.ClienteProduto.pendencia_financeira){
			            	$('#RecebsmTransportador').parent().addClass('error').append('<div class=\"help-block error-message\">Entrar em Contato com o Departamento Financeiro através dos telefones:<br />(11) 3443-2517.<br />(11) 3443-2587.<br />(11) 3443-2601.</div>');
			            }else if(data.ClienteProduto.pendencia_juridica){
			            	$('#RecebsmTransportador').parent().addClass('error').append('<div class=\"help-block error-message\">Entrar em Contato com o Departamento Jurídico através dos telefones:<br />(11) 5079-2572.<br/>(11) 3443-2572.');
			            }else if(data.ClienteProduto.pendencia_comercial){
			            	$('#RecebsmTransportador').parent().addClass('error').append('<div class=\"help-block error-message\">Serviço não disponível para o embarcador e transportador selecionados. Favor entrar em contato com o Departamento Comercial.');
			            }
				    }else{
				    	$('#RecebsmTransportador').parent().addClass('error').append('<div class=\"help-block error-message\">Serviço não disponível para o embarcador e transportador selecionados. Favor entrar em contato com o Departamento Comercial.');
				    }       
			            $('.emba_tran').unblock();
		        	}
				});
			}
		 });

		$(document).on('change','#RecebsmTransportador, #RecebsmGerenciadora',function(){
			$('#RecebsmCodigoDocumento').blur();
		});


		$('#RecebsmMonitorarRetorno').change(function(){
			if($(this).is(':checked'))
				$('#MonitorarRetorno').show();
			else
				$('#MonitorarRetorno').hide();
		});

		".($isPost ? "$('#RecebsmMonitorarRetorno').change();carregar_alvos_destino(false);" : '')."
		".((!$isPost && !empty($this->data['Recebsm']['codigo_usuario'])) ? "$('#RecebsmCodigoUsuario').change();carregar_alvos_destino(false);" : '')."
		".(isset($janelas_cliente) ? "$('.janela_select').show();$('.janela').hide()" : '')."


		function embarcador_transportador(){
			var cliente_codigo 	= $('#RecebsmCodigoCliente').val();
			var embarcador 		= $('#RecebsmEmbarcador');
			var transportador 	= $('#RecebsmTransportador');
			var gerenciadora 	= $('#RecebsmGerenciadora');
			var produtos 		= $('#RecebsmAlvoDestino0RecebsmNota0Carga');
			var loadCount 		= 4;

			if(cliente_codigo){
				embarcador.html('<option value=\'\'>Aguarde...</option>');
			    transportador.html('<option value=\'\'>Aguarde...</option>');
			    gerenciadora.html('<option value=\'\'>Aguarde...</option>');
			    produtos.html('<option value=\'\'>Aguarde...</option>');

				$.ajax({
			        'url': baseUrl + 'solicitacoes_monitoramento/lista_gerenciadoras_pessoa_jur/' + cliente_codigo + '/' + Math.random(),
			        'success': function(data) {		            
			           	gerenciadora.html(data);
			           	hidde_liberacao($('select#RecebsmGerenciadora').val());
			            loadCount--;
			            if(loadCount <= 0){
							$('#fields-view').unblock();
			            }
		        	}
				});

				$.ajax({
			        'url': baseUrl + 'solicitacoes_monitoramento/lista_embarcadores/' + cliente_codigo + '/' + Math.random(),
			        dataType: 'json',
			        'success': function(data) {
			            if(data.tipo == 4)
			            	embarcador.attr('readonly',false);
			            else
			            	embarcador.attr('readonly',true);

			            embarcador.html(data.html);
			            loadCount--;
			            if(loadCount <= 0){
							$('#fields-view').unblock();
			            }
		        	}
				});

				$.ajax({
			        'url': baseUrl + 'solicitacoes_monitoramento/lista_transportadores/' + cliente_codigo + '/' + Math.random(),
			        dataType: 'json',
			        'success': function(data) {
			            if(data.tipo == 4)
			            	transportador.attr('readonly',true);
			            else
			            	transportador.attr('readonly',false);

			            transportador.html(data.html);
			            loadCount--;
			            if(loadCount <= 0){
							$('#fields-view').unblock();
			            }
		        	}
				});
				
				$.ajax({
			        'url': baseUrl + 'solicitacoes_monitoramento/carregar_configuracao_cliente/' + cliente_codigo + '/' + Math.random(),
			        dataType: 'json',
			        'success': function(data) {
			        	if(data){
			        		if(data.TVppjValorPadraoPjur.vppj_monitorar_retorno){
			            		$('#MonitorarRetorno').show();
			            		$('#RecebsmMonitorarRetorno').click();
			            	}else{
			            		$('#MonitorarRetorno').hide();
			            	}
			            	$('#RecebsmTemperatura').val(data.TVppjValorPadraoPjur.vppj_temperatura_de);
			            	$('#RecebsmTemperatura2').val(data.TVppjValorPadraoPjur.vppj_temperatura_ate);
							
							if(data.TVppjValorPadraoPjur.vppj_temperatura_de && data.TVppjValorPadraoPjur.vppj_temperatura_de){
								$('#RecebsmEscolhaTemperatura').val('1');
							} else {
								$('#RecebsmEscolhaTemperatura').val('2');
							}
							mostraFaixaTemperatura();

			        	}
			            loadCount--;
			            if(loadCount <= 0){
							$('#fields-view').unblock();
			            }
		        	}
				});

			}

			
			return false;
		}

		function carregar_janelas(id, alvo){	
			var cliente_codigo 	= $('#RecebsmCodigoCliente').val();
			if(id>=0 && alvo > 0){
				bloquearDiv($('div.destino'));				
				$.ajax({
			        'url': baseUrl + 'solicitacoes_monitoramento/lista_janelas/' + cliente_codigo + '/' + alvo + '/' + Math.random(),
			        dataType: 'json',
			        'success': function(data) {			        	
						if(data.html){
				        	$('table[data-index=\"'+id+'\"] .janela_select').hide();
							$('table[data-index=\"'+id+'\"] .janela').hide();
							$('table[data-index=\"'+id+'\"] .janela_select select').html(data.html);
							$('table[data-index=\"'+id+'\"] .janela_select').show();
							$('div.destino').unblock();
			            }else{
							carregar_janelas(id,false);
			            }
		        	},
		        	'error': function(){		        		
		        		$('div.destino').unblock();
		        	}
				});
			}else if(id){				
				$.ajax({
			        'url': baseUrl + 'solicitacoes_monitoramento/lista_janelas/' + cliente_codigo + '/' + Math.random(),
			        dataType: 'json',
			        'success': function(data) {
			        	$('table[data-index=\"'+id+'\"] .janela_select').hide();
						$('table[data-index=\"'+id+'\"] .janela').hide();
						if(data.html){
							$('table[data-index=\"'+id+'\"] .janela_select select').html(data.html);
							$('table[data-index=\"'+id+'\"] .janela_select').show();
			            }else{
							$('table[data-index=\"'+id+'\"] .janela').show();
			            }
			            $('div.destino').unblock();
		        	},
		        	'error': function(){
						$('div.destino').unblock();
		        	}
				});
			}else{
				$.ajax({
			        'url': baseUrl + 'solicitacoes_monitoramento/lista_janelas/' + cliente_codigo + '/' + Math.random(),
			        dataType: 'json',
			        'success': function(data) {
			        	$('.janela_select').hide();
						$('.janela').hide();
						if(data.html){
							$('.janela_select select').html(data.html);
							$('.janela_select').show();
			            }else{
							$('.janela').show();
			            }
			            $('div.destino').unblock();
		        	},
		        	'error': function(){
						$('div.destino').unblock();
		        	}
				});
			}
		}

		function carregar_alvos_destino(id){			
			var cliente_codigo 	= $('#RecebsmCodigoCliente').val();
			var refe_codigo_origem = $('#RecebsmRefeCodigoOrigem').val();
			if(refe_codigo_origem){
				if(id){
					bloquearDiv($('table[data-index=\"'+id+'\"]'));
					$.ajax({
				        'url': baseUrl + 'solicitacoes_monitoramento/lista_alvos_destino/' + cliente_codigo + '/'+ refe_codigo_origem +'/' + Math.random(),
				        dataType: 'json',
				        'success': function(data) {
							$('table[data-index=\"'+id+'\"] .refe_codigo_destino_select').hide();
							$('table[data-index=\"'+id+'\"] .refe_codigo_destino').hide();
				            if(data.html){
								$('table[data-index=\"'+id+'\"] .refe_codigo_destino_select select').html(data.html);
								$('table[data-index=\"'+id+'\"] .refe_codigo_destino_select select').val($('table[data-index=\"'+id+'\"] .refe_codigo_destino input:first').val());
								$('table[data-index=\"'+id+'\"] .refe_codigo_destino_select').show();
								
				            }else{				            	
								$('table[data-index=\"'+id+'\"] .refe_codigo_destino').show();
				            }
				            $('table[data-index=\"'+id+'\"]').unblock();
				            atualiza_previsao_destinos(id);
			        	}
					});

				}else{
					bloquearDiv($('table.destino'));
					$.ajax({
				        'url': baseUrl + 'solicitacoes_monitoramento/lista_alvos_destino/' + cliente_codigo + '/'+ refe_codigo_origem +'/' + Math.random(),
				        dataType: 'json',
				        'success': function(data) {
							$('.refe_codigo_destino_select').hide();
							$('.refe_codigo_destino').hide();
				            if(data.html){
								$('.refe_codigo_destino_select select').html(data.html);
								$('.refe_codigo_destino_select').show();
								$('.refe_codigo_destino').each(function(){
									$(this).parents('table:first').find('.refe_codigo_destino_select select').val($(this).find('input:first').val());
									if( ( $(this).parents('table:first').find('.refe_codigo_destino_select select').val( ) ) ){
                                   		$(this).parents('table:first').find('.refe_codigo_destino').find(':input').val( $(this).parents('table:first').find('.refe_codigo_destino_select select').val( ));
									}
								});
				            }else{
								$('.refe_codigo_destino').show();
				            }
				            $('table.destino').unblock();
				            atualiza_previsao_destinos(0);
			        	}
					});
				}
			}else{
				$('.refe_codigo_destino_select').hide();
				$('.refe_codigo_destino').show();
			}
		}

		$(document).on('change','.refe_codigo_destino_select select',function(){
			var table = $(this).parents('table:first');
			var destino_id = table.attr('data-index');
			$('#RecebsmAlvoDestino'+destino_id+'RefeCodigo').val($(this).val());
			$('#RecebsmAlvoDestino'+destino_id+'RefeCodigoVisual').val($(this).find('option:selected').text());
		});

		$(document).on('change', 'table.destino input[type=hidden]', function(){
			id   = $(this).closest('table').data('index');
			alvo = $(this).val();
			if(alvo != ''){
				carregar_janelas(id, alvo);
				atualiza_previsao_destinos(id);
			}
		});

		$('#RecebsmRefeCodigoOrigem, #RecebsmHoraInc, #RecebsmDtaInc').change(function(){
			carregar_alvos_destino(false);
			if($('#RecebsmRefeCodigoOrigem').val()!=''){
				atualiza_previsao_destinos(0);
			}
		});

		$(document).on('click','a.novo-destino',function(){
			contador_destino++;
			var conteiner = $('div.destino');
			var cliente_codigo 	= $('#RecebsmCodigoCliente').val();
			bloquearDiv($('div.destino'));
			
			$.ajax({
				url: baseUrl + 'solicitacoes_monitoramento/novo_destino_facilitada/'+ (contador_destino-1) +'/'+ $('#RecebsmTransportador').val() +'/'+ $('#RecebsmEmbarcador').val() +'/'+ Math.random(),
				dataType: 'html',
				success: function(data){
					conteiner.prepend(data);
					setup_datepicker();
					setup_time();
					setup_mascaras();
					carregar_alvos_destino((contador_destino-1));
					carregar_janelas((contador_destino-1), false);
				},
				complete: function(){
					$.placeholder.shim();
					$('div.destino').unblock();
				}
			});
		});

		$(document).on('click', 'a.novo-nota-fiscal',function(){
			var conteiner = $(this).parents('tbody:first');
			var cliente_codigo 	= $('#RecebsmCodigoCliente').val();
			var table = $(this).parents('table:first');
			bloquearDiv($('div.destino'));
			$.ajax({
				url: baseUrl + 'solicitacoes_monitoramento/novo_nota_fiscal_facilitada/'+(contador_destino-1)+'/'+ conteiner.children('tr').length +'/'+ cliente_codigo +'/'+ Math.random(),
				dataType: 'html',
				success: function(data){
					conteiner.append(data);
					setup_mascaras();
				},
				complete: function(){
					$.placeholder.shim();
					$('div.destino').unblock();
				}
			});
		});

		$(document).on('click','a.novo-nota-remove',function(){
			$(this).parents('tr:eq(0)').remove();
			return false;
		});

		$(document).on('click','a.novo-destino-remove',function(){
			tabela = $(this).parents('table:eq(0)');
			id = tabela.data('index');
			tabela.remove();

			id = id+1;			
			atualiza_previsao_destinos(id);
			return false;
		});

		setup_mascaras();

		hidde_liberacao($('select#RecebsmGerenciadora').val());

		$(document).on('change', 'select#RecebsmGerenciadora',function(){
			hidde_liberacao($(this).val());
		});

		function hidde_liberacao(valor){
			var liberacao = $('#RecebsmLiberacao');
			if( valor == 4 || valor == '' || valor == 1){
				liberacao.val('');
				liberacao.parent().css('display','none');

			} else {
				liberacao.parent().css('display','block');

			}
		}

		$(document).on('change', 'select#RecebsmTransportador',function(){
			bloquearDiv( $('#itinerario') );
			carregar_produtos($('#RecebsmTransportador').val(),$('#RecebsmEmbarcador').val());
		});
		
		$(document).on('change', 'select#RecebsmEmbarcador',function(){
			carregar_produtos($('#RecebsmTransportador').val(),$('#RecebsmEmbarcador').val());
		});

		$(document).on('blur','#RecebsmCodigoDocumento',function(){
			jQuery('.motorista-data .documento').removeClass('error').find('.error-message').remove();
			carrega_motorista($(this).val());
			$.placeholder.shim();
			return false;
		});

		function carrega_motorista( cpf ) {				
			if(consultando){
				return false;
			}
			if( cpf ) {
				$('#ProfissionalCodigo').val('');
				$('#RecebsmTelefone').val('');
				$('#RecebsmRadio').val('');
				$('#RecebsmNome').val('');									
				if (validarCPF( cpf )) {
					$('.motorista-nao-encontrado').remove();
					$('#RecebsmNome').val('Aguarde...');
					bloquearDiv( $('.motorista-data') );
					$.ajax({
						url: baseUrl + 'solicitacoes_monitoramento/busca_dados_motorista/'+ cpf +'/'+ Math.random(),
						type: 'post',
						dataType: 'json',
						success: function(data){
							if(jQuery('.motorista-data .documento .error-message').html() == 'Motorista não cadastrado' || jQuery('.motorista-data .documento .error-message').html() == 'DESCONHECIDO')
								jQuery('.motorista-data .documento .error-message').remove();
							jQuery('.motorista-data .motorista-nao-encontrado').remove();
							if(data && data.nome){
								$('#ProfissionalCodigo').val(data.codigo);
								$('#ProfissionalEstrangeiro').val(data.estrangeiro);
								$('#RecebsmNome').val(data.nome);
								$('#RecebsmTelefone').val(data.telefone);
								$('#RecebsmRadio').val(data.radio);
								if( $('#ProfissionalEstrangeiro').val() == 0 && $('#RecebsmTransportador').val() && $('#ProfissionalCodigo').val() ){
									consulta_motorista_tlc( $('#RecebsmCodigoDocumento').val(),  $('#RecebsmCodigoCliente').val(), $('#RecebsmClienteTipo').val(), $('select#RecebsmEmbarcador').val(), $('select#RecebsmTransportador').val(), $('select#RecebsmGerenciadora').val(), $('#RecebsmPlaca').val() );
								}else{
									$('.motorista-data').unblock();
								}
							}else{
								$('#RecebsmNome').val('');

								var a = $('<a class=\"btn btn-mini btn-primary\">Adicionar Motorista</a>').click(function(event){
									open_dialog(baseUrl + 'profissionais/incluir/' + cpf, 'Adicionar motorista', 572)
									return false;
								});
								jQuery('.motorista-data').append(jQuery('<div class=\"control-group error motorista-nao-encontrado\" style=\"clear:both\">').append('<div class=\"help-inline\" style=\"padding: 0;\">Motorista não cadastrado</div>').append(a));
								$('.motorista-data').unblock();
							}
						}
					});
				} else {
					$('.motorista-data .documento').addClass('error').append('<div class=\"help-block error-message\">CPF inválido</div>');
					$('.motorista-nao-encontrado').remove();
				}
			}
		}

		function consulta_motorista_tlc(codigo_documento, codigo_cliente, cliente_tipo, embarcador, transportador, gerenciadora, placa, placa_carreta ){
			if(parseInt(codigo_documento) > 0 && validarCPF(codigo_documento)){
				if(embarcador.trim() == ''){
					embarcador = transportador;
				}
				$('.motorista-data .documento').removeClass('error').find('.error-message').remove();
				consultando = true;	

				$.ajax({
					url: baseUrl + 'solicitacoes_monitoramento/consulta_motorista_tlc/'+ codigo_documento.trim() +'/'+ codigo_cliente.trim() +'/'+ cliente_tipo.trim() +'/'+embarcador.trim()+'/'+transportador.trim()+'/'+ gerenciadora.trim() +'/'+ placa,
					type: 'post',
					dataType: 'json',
					success: function(data){
						if(data.perfil_adequado == false){
							$('.motorista-data .documento').addClass('error').append('<div class=\"help-block error-message\">Viagem não adequada ao risco.<br />Favor entrar em contato nos telefones<br />(11) 5079-2326 das 08:00 às 18:00 e<br />(11) 5079-2323 das das 18h00 às 08h00.<br />Solicite falar com o encarregado do setor.</div>');
						}
						var input = document.createElement('input');
						input.setAttribute('type', 'hidden');
						input.setAttribute('name', 'data[Recebsm][codigo_log_faturamento]');
						input.setAttribute('id', 'RecebsmCodigoLogFaturamento');
						
						if (data.codigo_log_faturamento) {
							input.setAttribute('value', data.codigo_log_faturamento);
						} else {
							input.setAttribute('value', '');
						}
						
						document.getElementById('RecebsmIncluirFacilitadaForm').appendChild(input);
						$('.motorista-data').unblock();
						consultando = false;
					}
				});
			}
		}


		function atualiza_previsao_destinos(id){

			if($('#RecebsmAlvoDestino'+id+'RefeCodigoSelect').length && $('#RecebsmAlvoDestino'+id+'RefeCodigoSelect').val() > 0)
				destino = $('#RecebsmAlvoDestino'+id+'RefeCodigoSelect').val();
			else
				destino = $('#RecebsmAlvoDestino'+id+'RefeCodigo').val();

			if(id == 0){
				origem = $('#RecebsmRefeCodigoOrigem').val();
				data_inicio = $('#RecebsmDtaInc').val();
				hora_inicio = $('#RecebsmHoraInc').val();
			}else{		
				anterior = id-1;
				while((!$('table.destino[data-index='+anterior+']').length) && anterior > 0){
					anterior = anterior-1;
				}				
				
				if(anterior >= 0){
					origem = $('#RecebsmAlvoDestino'+anterior+'RefeCodigo').val();
					data_inicio = $('#RecebsmAlvoDestino'+anterior+'DataFinal').val();
					hora_inicio = $('#RecebsmAlvoDestino'+anterior+'HoraFinal').val();
				}else{
					origem = 0;
				}
			}	

			if(origem > 0 && destino > 0){
				if(data_inicio.trim() != '' && hora_inicio.trim() != ''){
					inicio = data_inicio + ' ' + hora_inicio;
					
					$('table.destino input.hora-final, table.destino input.data').addClass('ui-autocomplete-loading');
					
					anterior = id-1;
					while(($('table.destino[data-index='+anterior+']').length) || anterior >=0){
						$('#RecebsmAlvoDestino'+anterior+'DataFinal, #RecebsmAlvoDestino'+anterior+'HoraFinal').removeClass('ui-autocomplete-loading');						
						anterior = anterior-1;
					}

					$.ajax({
						url: baseUrl + 'solicitacoes_monitoramento/previsao_chegada',
						type: 'post',
						dataType: 'json',				
						data: {
							'id': id, 
							'inicio': inicio, 
							'origem': origem, 
							'destino': destino
						},
						success: function(data){
							if(data != 0){
								data = data.split(' ');
								$('#RecebsmAlvoDestino'+id+'DataFinal').val(data[0]);
								$('#RecebsmAlvoDestino'+id+'HoraFinal').val(data[1]);
								maxid = $('table.destino:first');								
								maxid = maxid.data('index');								
								proximo = id;
								while(proximo <= maxid){
									proximo = proximo+1;
									if($('table.destino[data-index='+proximo+']').length){
										atualiza_previsao_destinos(proximo);
										break;
									}
								}
							}
						},
						complete: function(data){							
							$('#RecebsmAlvoDestino'+id+'DataFinal, #RecebsmAlvoDestino'+id+'HoraFinal').removeClass('ui-autocomplete-loading');							
						}
					});
				}
			}	
		}	


	})"
)) ?>