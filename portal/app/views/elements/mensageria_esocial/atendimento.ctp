<div class='row-fluid inline'>
	<?php echo $this->Form->hidden('editando', array('value' => $edit_mode)); ?>
	<div class="row-fluid">
		<?php echo $this->Buonny->input_codigo_medico_readonly($this, 'codigo_medico', 'Coord PCMSO', 'Coord PCMSO','Atestado', null, 'numero_conselho_pcmso', 'uf_conselho_pcmso', 'nome_medico_pcmso', 'cpf_medico_pcmso'); ?>
		<?php echo $this->BForm->input('numero_conselho_pcmso', array('style' => 'width: 80px;', 'label' => 'CRM', 'title' => ('CRM'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['numero_conselho'] : '')); ?>
		<?php echo $this->BForm->input('uf_conselho_pcmso', array('style' => 'width: 50px;', 'label' => 'UF', 'title' => ('UF'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['conselho_uf']  : '')); ?>
		<?php echo $this->BForm->input('nome_medico_pcmso', array('style' => 'width: 260px;', 'label' => 'Nome do Médico', 'title' => ('NOME'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['nome']  : '')); ?>
		<?php echo $this->BForm->input('cpf_medico_pcmso', array( 'class' => 'input-medium cpf', 'label' => 'CPF do Médico', 'title' => ('CPF'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['cpf']  : '')); ?>
	</div>

	<div class="row-fluid">
		<h4>Período de Afastamento:</h4>	
	</div>
	<div class="row-fluid">
		<i class="icon-question-sign"></i> 
		<label class="inline-block margin-right-15" for="habilita_afastamento_em_horas">Afastamento em horas: </label> 
		<?php echo $this->BForm->checkbox('habilita_afastamento_em_horas', array('value' => 1, 'checked' => $this->data['Atestado']['habilita_afastamento_em_horas']? true : false, 'id' => 'habilita_afastamento_em_horas', 'style' => 'margin: 0')); ?>
	</div>
	<?php echo $this->Form->hidden('codigo_cliente_funcionario', array('value' => $codigo_cliente_funcionario)); ?>
	<?php echo $this->Form->hidden('codigo_funcionario_setor_cargo', array('value' => $codigo_funcionario_setor_cargo)); ?>
	<div>&nbsp;</div>
	<div class="row-fluid  inline">	
		<label>Período de afastamento:</label>
		<div class="span3">
			<?php echo $this->BForm->input('data_afastamento_periodo', array('label' => false, 'value' => !empty($this->data['Atestado']['data_afastamento_periodo'])? $this->data['Atestado']['data_afastamento_periodo'] : date('d/m/Y'), 'before' => 'De: ', 'place-holder' => 'Afastamento', 'type' => 'text', 'class' => 'datepickerjs date input-small form-control', 'multiple')); ?>
		</div>
		<div class="span3">
			<?php echo $this->BForm->input('data_retorno_periodo', array('label' => false, 'before' => 'Até: ', 'place-holder' => 'Retorno', 'type' => 'text', 'class' => 'datepickerjs date input-small form-control', 'multiple')); ?>
		</div>
		<div class="span3">
			<?php echo $this->Form->input('afastamento_em_dias', array('label' => false, 'class' => 'form-control span3', 'id' => 'dias_afastado', 'before' => 'Dias afastado: ')); ?>
		</div>
	</div>
	<div class="row-fluid">
		<label>Período de horas:</label>
		<div class="span3">
			<?php echo $this->Form->input('hora_afastamento',array('label' => false, 'before' => 'De: ',  'type' => 'text', 'class' => 'hora form-control', 'multiple', 'style' => 'width: 40px')); ?>
		</div>
		<div class="span3">
			<?php echo $this->Form->input('hora_retorno', array('label' => false, 'before' => 'Até: ', 'type' => 'text', 'class' => 'hora form-control', 'multiple', 'style' => 'width: 40px')); ?>
		</div>
		<div class="span3">
			<?php echo $this->Form->input('afastamento_em_horas', array('label' => false, 'class' => 'form-control span3', 'id' => 'horas_afastado', 'before' => 'Horas afastado: ')); ?>
		</div>
	</div>
	<div>&nbsp;</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('codigo_motivo_licenca', array('class' => 'input-xlarge', 'label' => 'Motivo da Licença', 'options' => $MotivoAfastamento, 'empty' => 'Selecione uma opção')); ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('codigo_motivo_esocial', array('label' => 'Motivo da Licença (Tabela 18 - eSocial)', 'options' => $motivo_afastamento_esocial, 'empty' => 'Selecione','class' => 'js-example-basic-single','style'=>'width : 538px')); ?>
	</div>
	<div  id="afastamento" class="row-fluid inline">
		<div>
			<?php echo $this->BForm->input('motivo_afastamento', array('class' => 'input-xlarge-motivo-afast', 'options' => array('S' => 'Sim', 'N' => 'Não'), 'empty' => 'Selecione','label' => 'Afastamento decorre de mesmo motivo de afastamento anterior?', )); ?>
		</div>
		<div>
			<?php echo $this->BForm->input('origem_retificacao', array('class' => 'input-medium', 'style'=>'width : 180px', 'options' => array('1' => '1 - Por iniciativa do empregador', '2' => '2 - Revisão Administrativa;', '3' => '3 - Determinação Judicial'), 'empty' => 'Selecione','label' => 'Origem da retificação:', )); ?>
			<?php echo $this->BForm->input('tipo_acidente_transito', array('class' => 'input-medium', 'options' => array('1' => '1 - Atropelamento', '2' => '2 - Colisão', '3' => '3 - Outros'), 'empty' => 'Selecione','label' => 'Tipo de acidente de trânsito:' )); ?>	
		</div>
	</div>
	<div class="row-fluid inline">
		<div>
			<?php echo $this->BForm->input('tipo_processo', array('class' => 'input-medium', 'style'=>'width : 230px', 'options' => array('1' => '1 - Administrativo', '2' => '2 - Judicial;', '3' => '3 - Número de Benefício (NB) do INSS.'), 'empty' => 'Selecione','label' => 'Tipo de processo:', )); ?>
			<?php echo $this->BForm->input('numero_processo', array('type' => 'text','class' => 'input-medium', 'label' => 'Número do processo:')); ?>
			<?php echo $this->BForm->input('codigo_documento_entidade', array('class' => 'input-medium cnpj', 'label' => 'Orgão/Entidade:'));?>
		</div>
	</div>

	<div class="row-fluid inline">
		<?php echo $this->BForm->input('onus_remuneracao', array('label' => 'Ônus da Remuneração:', 'options' => array('1' => '1 - Apenas do Empregador', '2' => '2 - Apenas do Sindicato', '3' => '3 - Parte do Empregador, sendo a diferença e/ou complementação salarial paga pelo Sindicato.'), 'empty' => 'Selecione','class' => 'input-medium', 'style'=>'width : 538px')); ?>
		<div>
			<?php echo $this->BForm->input('onus_requisicao', array('label' => 'Ônus da cessão/requisição:', 'options' => array('1' => '1 - Ônus do Cedente', '2' => '2 - Ônus do Cessionário', '3' => '3 - Ônus do Cedente e Cessionário'), 'empty' => 'Selecione','class' => 'input-medium', 'style'=>'width : 200px')); ?>
		</div>
	</div>
	<div class="row-fluid inline">
		<div>
			<div>
				<!-- <?php echo $this->BForm->input('data_termino_afastamento', array('label' => false, 'before' => 'Data do término do afastamento: ', 'place-holder' => 'Retorno', 'type' => 'text', 'class' => 'datepickerjs date input-small form-control', 'multiple')); ?> -->
			</div>
		</div>
	</div>

	<div class="row-fluid inline">
		<?php echo $this->BForm->input('restricao', array('class' => 'input-xxlarge', 'label' => 'Restrição para o retorno')); ?>

		<?php $codigo_inciial_cid = 0; ?>

		<!-- CID10 -->
		<?php if(isset($codigo_atestado) && $codigo_atestado) : ?>

			<?php
			//verifica se existe dados de cids
			if(!empty($dados_cids)) {
				//conta quantos cids tem para este atestado
				$i = count($dados_cids)-1;
				//varre os dados do cid
				foreach ($dados_cids as $key4 => $value) {
			?>
					<div class="inputs-config span12 hide" style="margin-left: 0; margin-right: 1%; display: block;">
						<div class="checkbox-canvas">
							<div class="row-fluid">
								<div class="span12">
									<?php echo $this->BForm->input('cid10.'.$key4.'.doenca', array('value' => $value['Cid']['descricao'], 
															'class' => 'js-cid-10', 
															'label' => 'CID10', 
															'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
															'div' => 'control-group input text width-full padding-left-10', 
															'after' => '<span style="margin-top: -7px" class="btn btn-default js-remove-cid pointer pull-right" data-toggle="tooltip" title="Remover doença"><i class="icon-minus" ></i></span>')); ?>
								</div>
							</div>
						</div>
					</div>
			<?php 
				} //FINAL FOREACH $dadosCid

				$codigo_inciial_cid = $key4 + 1;
			}//fim if cid
			?>
		<?php endif; ?>

		<div class="js-encapsulado">
			<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
				<div class="checkbox-canvas">
					<div class="row-fluid">
						<div class="span12">
							<?php echo $this->BForm->input('cid10.'.$codigo_inciial_cid.'.doenca', 
								array('label' => 'CID10', 
									'class' => 'js-cid-10', 
									'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
									'div' => 'control-group input text width-full padding-left-10', 
									'required' => false, 
									'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span style="margin-top: -7px">')
							); 
							?>
						</div>	
					</div>
				</div>
			</div>
			<div class="js-memory hide">
				<div class="inputs-config hide span12" style="margin-left: 0; margin-right: 1%">
					<div class="checkbox-canvas">
						<div class="row-fluid">
							<div class="span12">
								<?php echo $this->BForm->input('cid10.xx.doenca', 
									array('label' => 'CID10', 
										'class' => 'js-cid-10', 
										'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
										'div' => 'control-group input text width-full padding-left-10', 
										'required' => false, 
										'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span style="margin-top: -7px">')
								); 
								?>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- FIM CID10 -->
	</div>
</div>

<?php echo $this->Javascript->codeBlock('$(function() { setup_mascaras(); setup_time(); });'); ?>
<?php echo $this->Buonny->link_js('atestados'); ?>

<script type="text/javascript">
	function checa_valor() {
		calculo_de_dias($('#AtestadoDataAfastamentoPeriodo').val(), $('#AtestadoDataAfastamentoPeriodo').val(), $('#AtestadoHoraAfastamento').val(), $('#AtestadoHoraRetorno').val());
		$('#dias_afastado').attr('disabled', true);
		$('#AtestadoDataRetornoPeriodo').attr('readonly', true).val($('#AtestadoDataAfastamentoPeriodo').val());
		$('#dias_afastado').val(0);
		$('#AtestadoDataAfastamentoPeriodo').focusout(function(event) {
			insere_data_ini_no_fim();
		});
	}
	function calculo_de_dias(data1, data2, hora1, hora2) {
		if(data1 != undefined && data1 != '' && data2 != undefined && data2 != '') {	

			data1 = data1.split('/');
			data2 = data2.split('/');
			data1 = new Date(data1[1]+'/'+data1[0]+'/'+data1[2]);
			data2 = new Date(data2[1]+'/'+data2[0]+'/'+data2[2]);

			if(data1 > data2) {
				$('#dias_afastado').val('');
				return 0;
			}

			var timeDiff = Math.abs(data2.getTime() - data1.getTime());
			var diff = Math.ceil(timeDiff / (1000 * 3600 * 24))+1
			$('#dias_afastado').val(diff);

			if(hora1 != undefined && hora1 != '__:__' && hora1 != '' && hora2 != undefined && hora2 != '__:__' && hora2 != '') {	
				hora1 = hora1.split(':');
				hora2 = hora2.split(':');
				hIni = parseInt(hora1[0]);
				mIni = parseInt(hora1[1]);
				hFim = parseInt(hora2[0]);
				mFim = parseInt(hora2[1]);
				if(mFim < mIni) {
					hFim = hFim-1;
					mFim = mFim+60;
				}
				var hDif = hFim - hIni;
				var mDif = mFim - mIni;
				if(diff > 0) {
					hDif = hDif*diff;
					mDif = mDif*diff;
				}
				while(mDif >= 60) {
					hDif = hDif+1;
					mDif = mDif-60;
				}

				if(hDif.toString().length < 2) {
					hDif = '0'+ hDif
				}
				if(mDif.toString().length < 2) {
					mDif = '0'+ mDif
				}
				if(hDif != NaN && mDif != NaN && hDif >= 0 && mDif >= 0) {
					$('#horas_afastado').val(hDif + ':' + mDif);			
				} else{
					$('#horas_afastado').val('');
				}
			} else {
				$('#horas_afastado').val('');
			}
		}
	}
	function insere_data_ini_no_fim() {
		$('#AtestadoDataRetornoPeriodo').val($('#AtestadoDataAfastamentoPeriodo').val());
		$('#dias_afastado').val(0);
	}
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
			calculo_de_dias($('#AtestadoDataAfastamentoPeriodo').val(), $('#AtestadoDataRetornoPeriodo').val(), $('#AtestadoHoraAfastamento').val(), $('#AtestadoHoraRetorno').val());
			if($('#habilita_afastamento_em_horas').is(':checked')) {
				insere_data_ini_no_fim();
			}
		}
	}).mask("99/99/9999");
	$(document).ready(function() {
		var habilita_hora = '<?php echo ($this->data['Atestado']['habilita_afastamento_em_horas'] == 1)? 1 : 0 ?>';
		var data_retorno = '<?php echo (!empty($this->data['Atestado']['data_retorno_periodo'])? $this->data['Atestado']['data_retorno_periodo'] : 0) ?>'
		if(habilita_hora == 1) {
			checa_valor();
		} else {
			if(data_retorno == 0) {
				$('#AtestadoDataRetornoPeriodo').val('<?php echo date('d/m/Y') ?>');
				calculo_de_dias($('#AtestadoDataAfastamentoPeriodo').val(), $('#AtestadoDataRetornoPeriodo').val(), $('#AtestadoHoraAfastamento').val(), $('#AtestadoHoraRetorno').val());
			}
		}
		$('#habilita_afastamento_em_horas').click(function(event) {
			if($(this).is(':checked')) {
				checa_valor()
			} else {
				calculo_de_dias($('#AtestadoDataAfastamentoPeriodo').val(), $('#AtestadoDataRetornoPeriodo').val(), $('#AtestadoHoraAfastamento').val(), $('#AtestadoHoraRetorno').val());
				$('#AtestadoDataAfastamentoPeriodo').unbind();
				$('#dias_afastado').attr('disabled', false);
				$('#AtestadoDataRetornoPeriodo').attr('readonly', false);
				$('#AtestadoDataAfastamentoPeriodo').unbind();
			}
		});
		$('#AtestadoHoraAfastamento').focusout(function(event) {
			calculo_de_dias($('#AtestadoDataAfastamentoPeriodo').val(), $('#AtestadoDataRetornoPeriodo').val(), $(this).val(), $('#AtestadoHoraRetorno').val());
		});
		$('#AtestadoHoraRetorno').focusout(function(event) {
			calculo_de_dias($('#AtestadoDataAfastamentoPeriodo').val(), $('#AtestadoDataRetornoPeriodo').val(), $('#AtestadoHoraAfastamento').val(), $(this).val(),);
		});
		$('#AtestadoDataAfastamentoPeriodo').focusout(function(event) {
			calculo_de_dias($('#AtestadoDataAfastamentoPeriodo').val(), $('#AtestadoDataRetornoPeriodo').val(), $('#AtestadoHoraAfastamento').val(), $(this).val(),);
		});
	});
</script>
<style type="text/css">
	.error-message{
		color: red;
	}
	label[for=AtestadoCodigoMedico]{
		margin-right:15px;
	}
</style>

<script type="text/javascript">
	jQuery(document).ready(function(){
		var codigoClienteFunc = $('#AtestadoCodigoClienteFuncionario').val();
		$('#afastamento').hide();
		retornaafastamento();

		$('#AtestadoCodigoMotivoEsocial').change(function(){
			retornaafastamento();
		});

		function retornaafastamento(){
			var value = $('#AtestadoCodigoMotivoEsocial').val();
	    	if(parseInt(value) == 1015 || parseInt(value) == 1017){
	    		$.ajax({
	                url: baseUrl + "atestados/buscar_afastamento_anterior/" + codigoClienteFunc,
	                dataType: "json",
	                beforeSend: function() {  
	                    bloquearDiv($(".form-procurar"));
	                },
	                success: function(data) {
	                	// console.log(data);
	                	if(data.return == 1) {
	            			$('#afastamento').show();
	                	}else{    		
							$('#afastamento').hide();
	    				}               
	                },
	                complete: function(data){
	                    ($(".form-procurar")).unblock();
	                }
	            });	
	    	}else{    		
				$('#afastamento').hide();
	    	}
		}	

		

		//funcao para validar o cnpj do campo orgao/entidade
		$('#AtestadoCodigoDocumentoEntidade').change(function(){			
			get_cnpj_alocado();
		});//fim funcao validacao orgao entidade

		get_cnpj_alocado = function(orgaoEntidade){
			
			var orgaoEntidade = $('#AtestadoCodigoDocumentoEntidade').val();
			var codigo_fsc = $('#AtestadoCodigoFuncionarioSetorCargo').val();

			// console.log(orgaoEntidade);
			// console.log(codigo_fsc);
			// return false;

			$.ajax({
                url: baseUrl + "Atestados/buscar_cnpj_alocado/" + codigo_fsc +"/" + orgaoEntidade.replace(/[^\d]+/g,''),
                dataType: "json",
                beforeSend: function() {  
                    bloquearDiv($(".form-procurar"));
                },
                success: function(data) {

                    if (data.return == 1) {
                    	 swal({
                            type: 'warning',
                            title: 'Atenção',
                            text: 'O CNPJ não pode ser o mesmo de alocação do Funcionário.'
                        });
                    }
                },
                complete: function(data){
	                ($(".form-procurar")).unblock();
	            }            
			});


		}


		 $('.js-example-basic-single').select2();

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
</script>