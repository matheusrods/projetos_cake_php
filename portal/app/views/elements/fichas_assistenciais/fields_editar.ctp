<style type="text/css">
.bad{
	color: #D06363 !important;
}
.good{
	color: #8bb863 !important;
}

.error-message{
	color: #b94a48;
}

.btn-disable {
    cursor: not-allowed;
    pointer-events: none;
    color: #c0c0c0 !important;
    background-color: #ffffff;
}

</style>
<div class='well'>
	<div class="bordered">
		<div class='row-fluid'>	
			<h5 class="text-center">DADOS PRINCIPAIS</h5>
			<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
			<?php echo $this->BForm->hidden('redir', array('value' => $redir)); ?>

			<div class="span2 no-margin-left checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('codigo_pedido_exame', array('value' => $dados['PedidoExame']['codigo'], 'label' => 'Cód. ped. exame:',  'style' => 'width: 86%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span4 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('empresa', array('value' => $dados['Empresa']['razao_social'], 'label' => 'Empresa:', 'style' => 'width: 95%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span3 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('unidade', array('value' => $dados['Unidade']['razao_social'], 'label' => 'Unidade:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span3 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('setor', array('value' => $dados['PedidoExame']['setor'], 'label' => 'Setor:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span3 no-margin-left checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('funcionario', array('value' => $dados['Funcionario']['nome'],'label' => 'Funcionário:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span3 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('cpf', array('value' => $dados['Funcionario']['cpf'],'label' => 'CPF:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span2 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('idade', array('value' => $dados[0]['idade'], 'label' => 'Idade:', 'style' => 'width: 86%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span2 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('data_admissao', array('value' => $dados['ClienteFuncionario']['admissao'], 'label' => 'Data de admissão:', 'style' => 'width: 86%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span2 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('sexo', array('value' => $dados[0]['sexo'], 'label' => 'Sexo:', 'style' => 'width: 86%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span3 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('cargo', array('value' => $dados['PedidoExame']['cargo'],'label' => 'Cargo:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span3 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('tipo_exame_ocipacional', array('label' => 'Tipo do exame ocupacional:', 'style' => 'width: 92%; margin-bottom: 0', 'value' => $dados['PedidoExame']['tipo_pedido_exame'], 'readonly' => true)) ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="clear"></div>
			<hr>
			<div class="span4 no-margin-left checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('codigo_medico', array('label' => 'Profissional:', 'options' => $dados['Medico'], 'empty' => ((!is_null($this->data))? null : 'Selecione'), 'style' => 'width: 95%; margin-bottom: 0', 'required' => 'required')) ?>
			</div>

			<?php if($this->data['hora_automatica'] == '0'): ?>
				<div class="span4 checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('hora_inicio_atendimento', array('label' => 'Horário de início de atendimento:', 'type' => 'time', 'style' => 'width: 31%; margin-bottom: 0')) ?>
				</div>
				
				<div class="span4 checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('hora_fim_atendimento', array('label' => 'Horário de finalização de atendimentosss:', 'type' => 'time', 'style' => 'width: 31%; margin-bottom: 0')) ?>
				</div>
			<?php else: ?>

				<div class="span4 checkbox-canvas padding-left-10">
					<label>Horário de início de atendimento:</label>
					<?php echo substr($this->data['FichaAssistencial']['hora_inicio_atendimento'], 0, 5); ?>
				</div>
				<div class="span4 checkbox-canvas padding-left-10">
					<label>Horário de finalização de atendimento:</label>
					<?php echo substr($this->data['FichaAssistencial']['hora_fim_atendimento'], 0, 5); ?>
				</div>

				<?php echo $this->BForm->hidden('hora_inicio_atendimento', array('value' => $this->data['FichaAssistencial']['hora_inicio_atendimento'])); ?>
				<?php echo $this->BForm->hidden('hora_fim_atendimento', array('value' => $this->data['FichaAssistencial']['hora_fim_atendimento'])); ?>

			<?php endif; ?>
		</div>
		<hr>
		<div class="bordered">
			<div class='row-fluid'>	
				<h5 class="text-center">MEDIÇÕES</h5>
				<div class="span4 no-margin-left checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('pa_sistolica', array('div' => false, 'label' => 'Pressão arterial (mmHg): <i class="adjust-icon icon-question-sign margin-right-5" data-toggle="tooltip" title="Insira as medidas em escala original. (Exemplo: 110/80)"></i>',  'style' => 'width: 40%; margin-bottom: 0', 'placeholder' => 'Sistólica')) ?>
					X
					<?php echo $this->BForm->input('pa_diastolica', array('label' => false, 'div' => false,  'style' => 'width: 40%; margin-bottom: 0', 'placeholder' => 'Diastólica')) ?>
				</div>
				<div class="span4 checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('pulso', array('div' => false, 'label' => 'Pulso (bpm - batimentos por minuto):', 'style' => 'width: 95%; margin-bottom: 0')) ?>
				</div>
				<div class="span4 checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('circunferencia_abdominal', array('div' => false, 'label' => 'Circunferência abdominal (cm):', 'style' => 'width: 95%; margin-bottom: 0')) ?>
				</div>
				<div class="span4 no-margin-left checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('peso_kg', array('div' => false, 'class' => 'calc_imc', 'label' => 'Peso (kg):',  'style' => 'width: 40%; margin-bottom: 0', 'placeholder' => 'Quilos', 'after' => '&nbsp;k')) ?>&nbsp;
					<?php echo $this->BForm->input('peso_gr', array('label' => false, 'class' => 'calc_imc', 'div' => false,  'style' => 'width: 40%; margin-bottom: 0', 'placeholder' => 'Gramas', 'after' => '&nbsp;g')) ?>
				</div>
				<div class="span4 checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('altura_mt', array('div' => false, 'class' => 'calc_imc', 'label' => 'Altura (cm):',  'style' => 'width: 40%; margin-bottom: 0', 'placeholder' => 'Metros', 'after' => '&nbsp;m')) ?>&nbsp;
					<?php echo $this->BForm->input('altura_cm', array('label' => false, 'class' => 'calc_imc', 'div' => false,  'style' => 'width: 40%; margin-bottom: 0', 'placeholder' => 'Centímetros', 'after' => '&nbsp;cm')) ?>
				</div>
				<div class="span4 checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('circunferencia_quadril', array('div' => false, 'label' => 'Circunferência quadril (cm):', 'style' => 'width: 95%; margin-bottom: 0')) ?>
				</div>

				<div class="span4 no-margin-left  checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('imc', array('div' => false, 'readonly' => 'readonly', 'label' => 'Índice de Massa Corpórea (IMC):', 'style' => 'width: 20%; margin-bottom: 0')) ?>
					<div style="float:right; margin-right: 30px;margin-top: 5px;">
						<p id="userImcMsg"  class="center label_status <?php echo ($this->data['FichaAssistencial']['imc'] > 25) ? 'bad' : 'good'; ?>" >
							<?php echo $this->data['FichaAssistencial']['msg_imc']; ?>
						</p>
					</div>
				</div>

			</div>
		</div>
		<hr>

		<?php 
		
		foreach ($questoes as $key => $grupoQuestao) { 

			$codigo_grupo_questao = $grupoQuestao['FichaAssistencialGQ']['codigo'];
			$gestacional_preventivos = '';
	
			if($dados[0]['sexo'] == 'Masculino' and ($codigo_grupo_questao == 4 OR 
				$codigo_grupo_questao == 5)){
				$gestacional_preventivos = 'style="display:none;"';
			}
			?>
			<div class="bordered" <?=$gestacional_preventivos?>>
				<div class='row-fluid inline'>	

					<div class="accordion" id="accordion2">

						<?php

						$class 							= '';
						$data_toggle 					= '';
						$data_parent 					= '';
						$href 							= '';
						$inicio_accordion_body 			= '';
						$final_accordion_body 			= '';
						$icone 							= '';

						if(	
							$codigo_grupo_questao == 2 OR 
							$codigo_grupo_questao == 3 OR 
							$codigo_grupo_questao == 4 OR 
							$codigo_grupo_questao == 5 OR 
							$codigo_grupo_questao == 6
						){

							$inicio_accordion_body = "<div id=" . $grupoQuestao['FichaAssistencialGQ']['codigo'] . " class='accordion-body collapse'>";
							$final_accordion_body = '</div>';

							$class 			= 'accordion-toggle';
							$data_toggle 	= 'data-toggle="collapse"';
							$data_parent 	= 'data-parent="#accordion2"';
							$href 			= "href='#{$grupoQuestao['FichaAssistencialGQ']['codigo']}'";
							$icone 			= '<i class="icon-plus"></i>';
						}

						?>
						<h5 class="text-center <?=$class?>" <?= $data_toggle ?> <?=$data_parent?> <?=$href?>>
							<?php echo $grupoQuestao['FichaAssistencialGQ']['descricao'] ?>
							<?=$icone?>
						</h5>
						<!-- FIM -->
						<?=$inicio_accordion_body?>
						<!-- MONTA AS QUESTOES -->
						<?php foreach ($grupoQuestao['FichaAssistencialQuestao'] as $key2 => $questao) {
							if($key2 > 0) {
								echo '<div class="span12 no-margin-left"><hr class="margin-top-7"></div>';		
							}

							//caso haja quebra de linha, aplique
							if(!empty($questao['quebra_linha'])) {
								echo '<div class="clear"></div>';
							}

							// caso modulo farmaco ativo, transforme a grid em span12
							if($questao['farmaco_ativo']) {
								$questao['span'] = 12;
							}
							?>

							<!-- CRIA UM CANVAS PARA AGRUPAR AS SUBQUESTOES -->
							<?php if(!empty($questao['FichaAssistencialSubQuest']) || $questao['multiplas_cids_ativo']) { ?>
							<div class="subgroup-question">
								<?php } ?>
								<!-- FIM -->

								<div class="inputs-config span<?php echo $questao['span'] ?>" style="margin-left: 0; margin-right: 1%">
									<div class="checkbox-canvas if-booleano">
										<?php 

										// se o campo for obrigatório
										$required = false;
										if($questao['obrigatorio']) $required = 'required';

										//atribui a label
										$label = $questao['label'];

										// se o campo tiver observações exiba-as
										$observacao = '';
										if(!empty($questao['observacao'])) $label = $label.' ('.$questao['observacao'].')';

										// se existir um help, exiba
										if(!empty($questao['ajuda'])) $label = $label.'<i class="adjust-icon icon-question-sign" data-toggle="tooltip" title="'.$questao['ajuda'].'"></i>';

										$default = null;
										$openMenu = null;

										switch ($questao['tipo']) {

											//CASO O CAMPO SEJA DO TIPO VARCHAR OU FLOAT:
											case 'VARCHAR':
											case 'FLOAT':
												echo $this->BForm->input('FichaAssistencialResposta.'.$questao['codigo'].'_resposta', array('label' => $label, 'style' => 'width: 95%; margin-bottom: 0; margin-top: -6px', 'div' => 'control-group input text width-full padding-left-10', 'required' => $required));
											break;

											//CASO O CAMPO SEJA DO TIPO BOOLEANO OU RADIO:
											case 'BOOLEANO':

												// se booleano deixar apenas as respostas "sim e "não" disponiveis
												$questao['conteudo'] = json_encode(array(1 => 'Sim', 0 => 'Não'));


												$default = 0;

											case 'RADIO':

												if(!empty($questao['opcao_selecionada'])) {
													$default = $questao['opcao_selecionada'];
												}

												if(!empty($questao['opcao_abre_menu_escondido'])) {
													$openMenu = $questao['opcao_abre_menu_escondido'];
												}

												// cria a label
												echo $this->BForm->label('FichaAssistencialResposta.'.$questao['codigo'].'_resposta', $label, array('for' => $questao['codigo']));

												// cria o input
												echo $this->BForm->input('FichaAssistencialResposta.'.$questao['codigo'].'_resposta', array('type' => 'radio', 'options' => (array)json_decode($questao['conteudo']), 'default' => $default, 'data-open' => $openMenu, 'hiddenField' => false, 'legend' => false, 'required' => $required, 'data-option' => $questao['opcao_exibe_label']));


												// cria o campo livre caso exista
												if(!empty($questao['parentesco_ativo'])) {
													echo $this->BForm->input('FichaAssistencialResposta.parentesco.'.$questao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos'=> 'Irmãos'), 'empty' => 'Parentesco', 'class' => 'adjust-parentesco', 'div' => false, 'label' => false));
												} elseif(!empty($questao['campo_livre_label'])) {
													echo $this->BForm->input('FichaAssistencialResposta.campo_livre.'.$questao['codigo'], array('type' => 'text', 'style' => 'width: 95%', 'label' => false, 'placeholder' => $questao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;', 'div' => 'js-label '.((!empty($questao['opcao_exibe_label']))? 'hide' : '')));
												}
											break;

											//CASO O CAMPO SEJA DO TIPO CHECKBOX:
											case 'CHECKBOX':

												// cria a label
												echo $this->BForm->label('FichaAssistencialResposta.'.$questao['codigo'].'_resposta', $label, array('for' => $questao['codigo']));

												// cria o input
												echo $this->BForm->input('FichaAssistencialResposta.'.$questao['codigo'].'_resposta', array('type' => 'select', 'multiple' => 'checkbox', 'options' => (array)json_decode($questao['conteudo']), 'label' => false, 'id' => false, 'hiddenField' => false,'required' => $required));

												// cria o campo livre caso exista
												if(!empty($questao['parentesco_ativo'])) {
													echo $this->BForm->input('FichaAssistencialResposta.parentesco.'.$questao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'empty' => 'Parentesco', 'class' => 'adjust-parentesco', 'div' => false, 'label' => false));
												} elseif(!empty($questao['campo_livre_label'])) {
													echo $this->BForm->input('FichaAssistencialResposta.campo_livre.'.$questao['codigo'], array('type' => 'text', 'style' => 'width: 95%', 'label' => false, 'placeholder' => $questao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;'));
												}
											break;

											//CASO O CAMPO SEJA DO TIPO FARMACO:
											case 'FARMACO':  ?>
											<div class="js-encapsulado-farmaco">

											<?php 
												if(!empty($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']])) {
													$i = count(($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']]))-1;
												
													$farmaco = 0; 
													foreach ($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']] as $key_farmaco => $value_farmaco) { ?>
														<div class="inputs-config span12 pull-right">
															<div class="checkbox-canvas">
																<div class="row-fluid pull-left padding-left-10 margin-top-5">
																	<div class="span3">
																		<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key_farmaco.'.farmaco', 
																			array('label' => false, 
																			'value' => $value_farmaco['farmaco'],
																			'placeholder' => 'Fármaco', 
																			'class' => 'js-farm', 
																			'div' => array('style' => 'width: 95%')
																			)
																	) ?>
																	</div>
																	<!-- FINAL span3-->
																	<div class="span3">
																		<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key_farmaco.'.posologia', 
																			array('label' => false, 
																			'value' => $value_farmaco['posologia'],
																			'placeholder' => 'Posologia', 
																			'class' => 'js-poso', 
																			'div' => array('style' => 'width: 95%'))
																		) ?>
																	</div>
																	<!-- FINAL span3 -->
																	<div class="span5">
																		<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key_farmaco.'.dose_diaria', 
																			array('label' => false, 
																			'value' => $value_farmaco['dose_diaria'],
																			'placeholder' => 'Dose diária', 
																			'class' => 'js-dose_diaria', 
																			'div' => array('style' => 'width: 95%'))
																	) ?>
																	</div>
																	<!-- FINAL span5 -->
																	<div class="span1">
																		<span class="btn btn-default 
																		<?= 
																			($key_farmaco == $i ) ? 
																			'js-add-farmaco' :
																			'js-remove-farmaco'
																		?>
																		pointer" data-toggle="tooltip" 
																		 title="<?= 
																			($key_farmaco == $i ) ? 
																			'Adicionar novo Medicamento de uso Regular' :
																			'Remover Medicamento de uso Regular'
																			?>
																		 ">
																			<i class="<?= 
																			($key_farmaco == $i ) ? 
																			'icon-plus' :
																			'icon-minus'
																			?>" ></i>
																		</span>
																	</div>
																	<!-- FINAL CLASS span1 -->
																</div>
																<!-- FINAL row-fluid-->
															</div>	
															<!-- FINAL CLASS checkbox-canvas-->
														</div>
														<!-- FINAL CLASS inputs-config -->
											<?php 
														$farmaco++; 
													} //FINAL FOREACH $this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']]
												}else{ ?>
												<div class="inputs-config span12 pull-right">
													<div class="checkbox-canvas">
														<div class="row-fluid pull-left padding-left-10 margin-top-5">
															<div class="span3">
																<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.farmaco', array('label' => false, 
																	'placeholder' => 'Fármaco', 
																	'class' => 'js-farm', 
																	'div' => array('style' => 'width: 95%'))
															) ?>
															</div>
															<!-- FINAL span3-->
															<div class="span3">
																<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.posologia', array('label' => false, 
																	'placeholder' => 'Posologia', 
																	'class' => 'js-poso', 
																	'div' => array('style' => 'width: 95%'))
																) ?>
															</div>
															<!-- FINAL span3 -->
															<div class="span5">
																<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.dose_diaria', array('label' => false, 
																	'placeholder' => 'Dose diária', 
																	'class' => 'js-dose_diaria', 
																	'div' => array('style' => 'width: 95%'))
															) ?>
															</div>
															<!-- FINAL span5 -->
															<div class="span1">
																<span class="btn btn-default js-add-farmaco pointer" data-toggle="tooltip" title="Adicionar novo Medicamento de uso Regular">
																	<i class="icon-plus" ></i>
																</span>
															</div>
														</div>
														<!-- FINAL row-fluid-->
													</div>	
													<!-- FINAL CLASS checkbox-canvas-->
												</div>
											<?php } ?>
									<!-- FINAL CLASS inputs-config -->
									<div class="hide js-memory-farmaco">		
										<div class="inputs-config hide span12 pull-right">
											<div class="checkbox-canvas">
												<div class="row-fluid pull-left padding-left-10 margin-top-5">
													<div class="span3">
														<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.farmaco', 
															array('label' => false, 
																'placeholder' => 'Fármaco', 
																'class' => 'js-farm', 
																'disabled' => true, 
																'div' => array('style' => 'width: 95%'))
														)	 
														?>
													</div>
													<div class="span3">
														<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.posologia', array(
															'label' => false, 
															'placeholder' => 'Posologia', 
															'class' => 'js-poso', 
															'disabled' => true, 
															'div' => array('style' => 'width: 95%'))) ?>
														</div>
														<div class="span5">
															<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.dose_diaria', array(
																'label' => false, 
																'placeholder' => 'Dose diária', 
																'class' => 'js-dose_diaria', 
																'disabled' => true, 
																'div' => array('style' => 'width: 95%'))
														) 
														?>
													</div>
													<div class="span1">
														<span class="btn btn-default js-add-farmaco pointer" data-toggle="tooltip" title="Adicionar novo Medicamento de uso Regular">
															<i class="icon-plus" ></i>
														</span>
													</div>
												</div>
											</div>
											<!-- FINAL CLASS checkbox-canvas-->
										</div>
										<!-- FINAL CLASS inputs-config -->
									</div>
									<!-- FINAL CLASS js-memory-farmaco -->

								</div>
								<?php 
								echo $this->BForm->hidden('FichaAssistencialResposta.'.$questao['codigo'].'_resposta', array('value' => '0')
							);
							?>
							<!-- FINAL CLASS js-encapsulado-farmaco --> 
							<?php 	
							break;

							case 'TEXTAREA': 

								echo $this->BForm->input('FichaAssistencialResposta.'.$questao['codigo'].'_resposta',
									array('label' => $label, 
										'style' => 'width:100%', 
										'div' => 'control-group input text width-full padding-left-10', 
										'required' => $required,
										'type' => 'textarea'
									)
								);
							break;

							case 'CID10': ?>
								<div class="js-encapsulado-cid10">
									<?php 
										
										if(!empty($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']])) {
											$i_cid10 = count(($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']]))-1;
													
											$cid10 = 0; 
											foreach ($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']] as $key_cid10 => $value_cid10) { ?>
												<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
													<div class="checkbox-canvas">
														<div class="row-fluid">
															<div class="span12">
																<?php 
																	echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key_cid10.'.doenca', 
																	array('label' => 'CID10', 
																		'value' => $value_cid10['doenca'],
																		'class' => 'js-cid10', 
																		'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
																		'div' => 'control-group input text width-full padding-left-10', 
																		'required' => $required, 
																		'after' => '<span style="margin-top: -7px" 
																					 class="btn btn-default 
																					 '. (($key_cid10 == $i_cid10 ) ? 
																					'js-add-cid10' : 'js-remove-cid10' ). '
																					 pointer pull-right" 
																					 data-toggle="tooltip" 
																				 	 title="
																				 	 '. (($key_cid10 == $i_cid10 ) ? 
																				'Adicionar novo doeça' : 'Remover doença' ). '
																				">
																				<i class="'.(($key_cid10 == $i_cid10 ) ? 'icon-plus' : 'icon-minus').'"></i>
																				</span>')
																); 
																?>
															</div>
															<!-- FINAL span12 -->
														</div>
														<!-- FINAL row-fluid -->
													</div>
													<!-- FINAL checkbox-canvas -->
												</div>
												<!-- FINAL inputs-config -->
										<?php 
												$cid10++; 
											} //FINAL FOREACH $this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']]
										}else{ ?>
											<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
												<div class="checkbox-canvas">
													<div class="row-fluid">
														<div class="span12">
															<?php 
															echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.doenca', 
																array('label' => 'CID10', 
																	'class' => 'js-cid10', 
																	'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
																	'div' => 'control-group input text width-full padding-left-10', 
																	'required' => $required, 
																	'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid10 pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span>')
															); ?>
														</div>
														<!-- FINAL span12 -->
													</div>
													<!-- FINAL row-fluid -->
												</div>
												<!-- FINAL checkbox-canvas -->
											</div>
											<!-- FINAL inputs-config -->
										<?php } ?>
									<div class="hide js-memory-cid10">		
										<div class="inputs-config hide span12" style="margin-left: 0; margin-right: 1%">
											<div class="checkbox-canvas">
												<div class="row-fluid">
													<div class="span12">
														<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.doenca', 
															array('disabled' => true, 
																'label' => 'CID10', 
																'class' => 'js-cid10', 
																'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
																'div' => 'control-group input text width-full padding-left-10', 
																'required' => $required, 
																'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid10 pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span style="margin-top: -7px">')
														); 
														?>
													</div>
													<!-- FINAL span12 -->
												</div>
												<!-- FINAL row-fluid -->
											</div>
											<!-- FINAL checkbox-canvas-->
										</div>
										<!-- FINAL inputs-config-->
									</div>
									<!-- FINAL js-memory -->
									<?php 
									echo $this->BForm->hidden('FichaAssistencialResposta.'.$questao['codigo'].'_resposta', array('value' => '0')
								);
								?>
								</div>
								<!-- FINAL js-encapsulado-cid10-->
							<?php 
							break;

						case 'PRESCRICAO': 
							// se booleano deixar apenas as respostas "sim e "não" disponiveis
							$questao['conteudo'] = json_encode(array(1 => 'Sim', 0 => 'Não'));
							$default = 0;

							echo $this->BForm->input('FichaAssistencialResposta.'.$questao['codigo'].'_resposta.exibe', array('type' => 'radio', 
								'options' => (array)json_decode($questao['conteudo']), 
								'default' => $default, 
								'data-open' => $openMenu, 
								'hiddenField' => false, 
								'legend' => false, 
								'required' => $required, 
								'data-option' => $questao['opcao_exibe_label'])
							); ?>
							<div class="div-oculta hide">
								<div class="js-encapsulado-farmaco">
									<?php if(!empty($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']])) {
														$i_prescricao = count(($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']]))-1;
																
														$prescricao = 0; 
														foreach ($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']] as $key_prescricao => $value_prescricao) { ?>

									<div class="inputs-config span12 pull-right">
										<div class="checkbox-canvas">
											<div class="row-fluid pull-left padding-left-10 margin-top-5">
												<div class="span3">
													<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key_prescricao.'.farmaco', array('label' => false, 
														'value' => $value_prescricao['farmaco'],
														'placeholder' => 'Fármaco', 
														'class' => 'js-farm', 
														'div' => array('style' => 'width: 95%'))
												) ?>
												</div>
												<!-- FINAL CLASS span3 -->
												<div class="span3">
													<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key_prescricao.'.posologia', array('label' => false, 
														'placeholder' => 'Posologia', 
														'value' => $value_prescricao['posologia'],
														'class' => 'js-poso', 
														'div' => array('style' => 'width: 95%'))
												) ?>
												</div>
												<!-- FINAL CLASS span3 -->
												<div class="span5">
													<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key_prescricao.'.dose_diaria', array('label' => false, 
														'placeholder' => 'Dose diária', 
														'value' => $value_prescricao['dose_diaria'],
														'class' => 'js-dose_diaria', 
														'div' => array('style' => 'width: 95%'))
												) ?>
												</div>
												<!-- FINAL CLASS span5 -->
												<div class="span1">
													<span class="btn btn-default 
														<?= ($key_prescricao == $i_prescricao ) ? 'js-add-farmaco' : 'js-remove-farmaco' ?>
														pointer" 
													data-toggle="tooltip" 
													 title="<?= 
														($key_prescricao == $i_prescricao ) ? 
														'Adicionar novo Medicamento de uso Regular' :
														'Remover Medicamento de uso Regular'
														?>
													 ">
														<i class="<?= 
														($key_prescricao == $i_prescricao ) ? 
														'icon-plus' :
														'icon-minus'
														?>" ></i>
													</span>
												</div>
												<!-- FINAL CLASS span1 -->
											</div>
											<!-- FINAL row-fluid -->
											<div class="row-fluid pull-left padding-left-10 margin-top-5">
												<div class="span6">
													<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key_prescricao.'.duracao', array('label' => false, 
														'placeholder' => 'Duração', 
														'value' => $value_prescricao['duracao'],
														'div' => array('style' => 'width: 95%'))
													) ?>
												</div>
												<!-- FINAL CLASS span6 --> 
												<div class="span6">
													<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key_prescricao.'.tipo_uso', 
														array('label' => false, 
															'options' => $opcoes_tipo_uso, 
															'empty' => 'Tipo e uso',
															'default' =>  $value_prescricao['tipo_uso'],
															'style' => 'width: 95%; margin-bottom: 0')
														) ?>
												</div>
												<!-- FINAL CLASS span6 --> 
												<?php echo $this->BForm->hidden('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key_prescricao.'.prescricao', array('value' => '1')); ?>
											</div>
											<!-- FINAL CLASS row-fluid-->	
										</div>
										<!-- FINAL CLASS checkbox-canvas-->
									</div>
									<!-- FINAL CLASS inputs-config -->
									<?php
										$prescricao++;
										} //FINAL FOREACH $this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']]
									}else{ ?>
										<div class="inputs-config span12 pull-right">
											<div class="checkbox-canvas">
												<div class="row-fluid pull-left padding-left-10 margin-top-5">
													<div class="span3">
														<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.farmaco', array('label' => false, 
															'placeholder' => 'Fármaco', 
															'class' => 'js-farm', 
															'div' => array('style' => 'width: 95%'))
													) ?>
													</div>
													<!-- FINAL CLASS span3 -->
													<div class="span3">
														<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.posologia', array('label' => false, 
															'placeholder' => 'Posologia', 
															'class' => 'js-poso', 
															'div' => array('style' => 'width: 95%'))
													) ?>
													</div>
													<!-- FINAL CLASS span3 -->
													<div class="span5">
														<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.dose_diaria', array('label' => false, 
															'placeholder' => 'Dose diária', 
															'class' => 'js-dose_diaria', 
															'div' => array('style' => 'width: 95%'))
													) ?>
													</div>
													<!-- FINAL CLASS span5 -->
													<div class="span1">
														<span class="btn btn-default js-add-farmaco pointer" data-toggle="tooltip" title="Adicionar novo Medicamento de uso Regular">
															<i class="icon-plus" ></i>
														</span>
													</div>
													<!-- FINAL CLASS span1 -->
												</div>
												<!-- FINAL row-fluid -->
												<div class="row-fluid pull-left padding-left-10 margin-top-5">
													<div class="span6">
														<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.duracao', array('label' => false, 
															'placeholder' => 'Duração', 
															'div' => array('style' => 'width: 95%'))
														) ?>
													</div>
													<!-- FINAL CLASS span6 --> 
													<div class="span6">
													<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.tipo_uso', 
														array('label' => false, 
															'options' => $opcoes_tipo_uso, 
															'empty' => 'Tipo e uso', 
															'style' => 'width: 95%; margin-bottom: 0')
														) ?>
													</div>
													<!-- FINAL CLASS span6 --> 
													<?php echo $this->BForm->hidden('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.prescricao', array('value' => '1')); ?>
												</div>
												<!-- FINAL CLASS row-fluid-->	
											</div>
											<!-- FINAL CLASS checkbox-canvas-->
										</div>
										<!-- FINAL CLASS inputs-config -->
									<?php } ?>

									<div class="hide js-memory-farmaco">		
										<div class="inputs-config hide span12 pull-right">
											<div class="checkbox-canvas">
												<div class="row-fluid pull-left padding-left-10 margin-top-5">
													<div class="span3">
														<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.farmaco', 
															array('label' => false, 
																'placeholder' => 'Fármaco', 
																'class' => 'js-farm', 
																'disabled' => true, 
																'div' => array('style' => 'width: 95%'))
														)	 
														?>
													</div>
													<!-- FINAL CLASS span3 -->
													<div class="span3">
														<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.posologia', array(
															'label' => false, 
															'placeholder' => 'Posologia', 
															'class' => 'js-poso', 
															'disabled' => true, 
															'div' => array('style' => 'width: 95%'))) ?>
														</div>
														<!-- FINAL CLASS span3 -->
														<div class="span5">
															<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.dose_diaria', array(
																'label' => false, 
																'placeholder' => 'Dose diária', 
																'class' => 'js-dose_diaria', 
																'disabled' => true, 
																'div' => array('style' => 'width: 95%'))
														) 
														?>
													</div>
													<!-- FINAL CLASS span5 -->
													<div class="span1">
														<span class="btn btn-default js-add-farmaco pointer" data-toggle="tooltip" title="Adicionar novo Medicamento de uso Regular">
															<i class="icon-plus" ></i>
														</span>
													</div>
													<!-- FINAL CLASS span1 -->
												</div>
												<!-- FINAL row-fluid --> 
												<div class="row-fluid pull-left padding-left-10 margin-top-5">
													<div class="span6">
														<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.duracao', 
															array('label' => false, 
																'placeholder' => 'Duração', 
																'disabled' => true, 
																'div' => array('style' => 'width: 95%'))
														) 
														?>
													</div>
													<!-- FINAL CLASS span6 --> 
													<div class="span6">
														<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.tipo_uso', 
															array('label' => false, 
																'options' => $opcoes_tipo_uso, 
																'empty' => 'Tipo e uso',
																'disabled' => true, 
																'style' => 'width: 95%; margin-bottom: 0')
														) 
														?>
													</div>
													<!-- FINAL CLASS span6 --> 
													<?php 
													echo $this->BForm->hidden('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.prescricao', array('value' => '1', 'disabled' => true)
												);
												?>
											</div>
											<!-- FINAL row-fluid --> 
										</div>
										<!-- FINAL CLASS checkbox-canvas-->
									</div>
									<!-- FINAL CLASS inputs-config -->
								</div>
								<!-- FINAL CLASS js-memory-farmaco -->
								</div>
								<!-- FINAL CLASS js-encapsulado-farmaco --> 
								<?php
								echo $this->BForm->input('FichaAssistencialResposta.'.$questao['codigo'].'_resposta.observacao',
									array('label' => 'Observação', 
										'style' => 'width:100%', 
										'div' => 'control-group input text width-full padding-left-10', 
										'required' => $required,
										'type' => 'textarea'
									)
								);
							?>
							</div>
							<?php
						break;
				} ?>

<!-- Monta o módulo fármaco -->
<?php if($questao['farmaco_ativo']) { ?>
<div class="row-fluid pull-left padding-left-10 margin-top-5">
	<div class="span4">
		<?php echo $this->BForm->input('FichaAssistencialResposta.campo_livre.'.$questao['codigo'].'.farmaco', array('label' => false, 'placeholder' => 'Fármaco', 'class' => 'js-farmaco', 'div' => array('style' => 'width: 95%'))) ?>
	</div>
	<div class="span4">
		<?php echo $this->BForm->input('FichaAssistencialResposta.campo_livre.'.$questao['codigo'].'.posologia', array('label' => false, 'placeholder' => 'Posologia', 'class' => 'js-posologia', 'div' => array('style' => 'width: 95%'))) ?>
	</div>
	<div class="span4">
		<?php echo $this->BForm->input('FichaAssistencialResposta.campo_livre.'.$questao['codigo'].'.dose_diaria', array('label' => false, 'placeholder' => 'Dose diária', 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
	</div>
</div>
<?php } ?>
<!-- fim modulo farmaco -->

</div>	
</div>	

<!-- MONTA AS SUBQUESTOES -->
<?php if(!empty($questao['FichaAssistencialSubQuest'])) { ?>
<div class="clear"></div>

<?php foreach ($questao['FichaAssistencialSubQuest'] as $key3 => $subquestao) { 

							//se houver quebra de linha, aplique
	if(!empty($subquestao['quebra_linha'])) {
		echo '<div class="clear"></div>';
	}

	// parametriza o nenu escondido
	$hide = '';
	if($questao['tipo'] == 'BOOLEANO' && (!isset($this->data['FichaAssistencialResposta'][$questao['codigo'].'_resposta']) || $this->data['FichaAssistencialResposta'][$questao['codigo'].'_resposta'] != 1)) {
		$hide = 'hide';
	} elseif($questao['tipo'] == 'RADIO' && $questao['opcao_abre_menu_escondido'] && isset($this->data['FichaAssistencialResposta'][$questao['codigo'].'_resposta']) != $questao['opcao_abre_menu_escondido'] ) {
		$hide = 'hide';
	}

	// caso modulo farmaco ativo, transforme a grid em span12
	if($subquestao['farmaco_ativo']) {
		$subquestao['span'] = 12;
	}
	?>

	<div class="inputs-config span<?php echo $subquestao['span'] ?> hides <?php  echo $hide ?>" style="margin-left: 0; margin-right: 1%">
		<div class="checkbox-canvas if-booleano">
			<?php 
									// se o campo for obrigatório
			$required = false;
			if($subquestao['obrigatorio']) $required = 'required';

									//atribui a label
			$label = $subquestao['label'];

									// se o campo tiver observações exiba-as
			$observacao = '';
			if(!empty($subquestao['observacao'])) $label = $label.' ('.$subquestao['observacao'].')';

									// se existir um help, exiba
			if(!empty($subquestao['ajuda'])) $label = $label.'<i class="adjust-icon icon-question-sign" data-toggle="tooltip" title="'.$subquestao['ajuda'].'"></i>';

			$default = null;
			$openMenu = null;

			switch ($subquestao['tipo']) {

				//CASO O CAMPO SEJA DO TIPO VARCHAR OU FLOAT:
				case 'VARCHAR':
				case 'FLOAT':
					echo $this->BForm->input('FichaAssistencialResposta.'.$subquestao['codigo'].'_resposta', array('label' => $label, 'style' => 'width: 95%; margin-bottom: 0; margin-top: -6px', 'div' => 'control-group input text width-full padding-left-10', 'required' => $required));
				break;

				//CASO O CAMPO SEJA DO TIPO BOOLEANO OU RADIO: 
				case 'BOOLEANO':

					// se booleano deixar apenas as respostas "sim e "não" disponiveis
					$subquestao['conteudo'] = json_encode(array(1 => 'Sim', 0 => 'Não'));
					$default = 0;
				case 'RADIO':
					if(!empty($subquestao['opcao_selecionada'])) {
						$default = $subquestao['opcao_selecionada'];
					}

					if(!empty($subquestao['opcao_abre_menu_escondido'])) {
						$openMenu = $subquestao['opcao_abre_menu_escondido'];
					}

											// cria a label
					echo $this->BForm->label('FichaAssistencialResposta.'.$subquestao['codigo'].'_resposta', $label, array('for' => $subquestao['codigo']));

											// cria o input
					echo $this->BForm->input('FichaAssistencialResposta.'.$subquestao['codigo'].'_resposta', array('type' => 'radio', 'options' => (array)json_decode($subquestao['conteudo']), 'data-open' => $openMenu, 'hiddenField' => false, 'legend' => false, 'required' => $required, 'suboption' => 1, 'data-option' => $subquestao['opcao_exibe_label']));

											// cria o campo livre caso exista
					if(!empty($subquestao['parentesco_ativo'])) {
						echo $this->BForm->input('FichaAssistencialResposta.parentesco.'.$subquestao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'default' => $default, 'empty' => 'Parentesco', 'class' => 'adjust-parentesco', 'div' => false, 'label' => false));
					} elseif(!empty($subquestao['campo_livre_label'])) {
						echo $this->BForm->input('FichaAssistencialResposta.campo_livre.'.$subquestao['codigo'], array('type' => 'text', 'style' => 'width: 95%', 'label' => false, 'placeholder' => $subquestao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;', 'div' => 'js-label '.((!empty($subquestao['opcao_exibe_label']) &&  empty($this->data['FichaAssistencialResposta']['campo_livre'][$subquestao['codigo']]) )? 'hide' : '')));
					}
				break;

				//CASO O CAMPO SEJA DO TIPO CHECKBOX:
				case 'CHECKBOX':

					// cria a label
					echo $this->BForm->label('FichaAssistencialResposta.'.$subquestao['codigo'].'_resposta', $label, array('for' => $subquestao['codigo']));

											// cria o input
					echo $this->BForm->input('FichaAssistencialResposta.'.$subquestao['codigo'].'_resposta', array('type' => 'select', 'multiple' => 'checkbox', 'options' => (array)json_decode($subquestao['conteudo']), 'label' => false, 'id' => false, 'hiddenField' => false, 'required' => $required));

											// cria o campo livre caso exista
					if(!empty($subquestao['parentesco_ativo'])) {
						echo $this->BForm->input('FichaAssistencialResposta.parentesco.'.$subquestao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'empty' => 'Parentesco', 'class' => 'adjust-parentesco', 'div' => false, 'label' => false));
					} elseif(!empty($subquestao['campo_livre_label'])) {
						if(is_array($this->data['FichaAssistencialResposta']['campo_livre'][$subquestao['codigo']])){
							echo $this->BForm->input('FichaAssistencialResposta.campo_livre.'.$subquestao['codigo'].'.0', array('type' => 'text', 'style' => 'width: 95%', 'label' => false, 'placeholder' => $subquestao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;'));
						} else {
							echo $this->BForm->input('FichaAssistencialResposta.campo_livre.'.$subquestao['codigo'], array('type' => 'text', 'style' => 'width: 95%', 'label' => false, 'placeholder' => $subquestao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;'));
						}
					}
				break;

			} ?>
			<!-- modulo farmaco -->
			<?php if($subquestao['farmaco_ativo']) { ?>
			<?php // echo $this->BForm->hidden('FichaAssistencialResposta.'.$subquestao['codigo'].'_resposta', array('value' => 1)); ?>
			<div class="row-fluid pull-left padding-left-10 margin-top-5">
				<div class="span4">
					<?php echo $this->BForm->input('FichaAssistencialResposta.campo_livre.'.$subquestao['codigo'].'.farmaco', array('label' => false, 'placeholder' => 'Fármaco', 'class' => 'js-farmaco', 'div' => array('style' => 'width: 95%'))) ?>
				</div>
				<div class="span4">
					<?php echo $this->BForm->input('FichaAssistencialResposta.campo_livre.'.$subquestao['codigo'].'.posologia', array('label' => false, 'placeholder' => 'Posologia', 'class' => 'js-posologia', 'div' => array('style' => 'width: 95%'))) ?>
				</div>
				<div class="span4">
					<?php echo $this->BForm->input('FichaAssistencialResposta.campo_livre.'.$subquestao['codigo'].'.dose_diaria', array('label' => false, 'placeholder' => 'Dose diária', 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
				</div>
			</div>
			<?php } ?>
			<!-- fim modulo farmaco -->

		</div>
	</div>	
	<?php } ?>

	<?php } ?>
	<!-- FIM MONTA AS SUBQUESTOES -->

	<!-- modulo add multiplas doencas -->
	<div class="js-encapsulado">		
		<?php 
		if($questao['multiplas_cids_ativo'] && empty($cid10)) {

		if(!empty($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']])) {
			$i = count(($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']]))-1;
			
			$e = 0; 
			foreach ($this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']] as $key4 => $value) {  
		?>
				<div class="inputs-config span12 hide" style="margin-left: 0; margin-right: 1%; display: block;">
					<div class="checkbox-canvas">
						<div class="row-fluid">
							<div class="span12">
								<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key4.'.doenca', array('value' => $value['doenca'], 
													'class' => 'js-cid-10', 
													'label' => 'CID10', 
													'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
													'div' => 'control-group input text width-full padding-left-10', 
													'required' => $required, 
													'after' => '<span style="margin-top: -7px" class="btn btn-default '.(($key4 == $i)? 'js-add-cid' : 'js-remove-cid').' pointer pull-right" data-toggle="tooltip" title="'.(($key4 == $i)? 'Adicionar doença' : 'Remover doença').'"><i class="'.(($key4 == $i)? 'icon-plus' : 'icon-minus').'" ></i></span>')); ?>
							</div>	
							<!-- FINAL CLASS span12-->
						</div>
						<!-- FINAL CLASS row-fluid-->
						<div class="row-fluid pull-left padding-left-10 margin-top-5">
							<div class="span4">
								<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key4.'.farmaco', array('value' => $value['farmaco'], 'class' => 'js-farmaco', 'label' => false, 'placeholder' => 'Fármaco', 'div' => array('style' => 'width: 95%'))) ?>
							</div>
							<!-- FINAL CLASS span4 -->
							<div class="span4">
								<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key4.'.posologia', array('value' => $value['posologia'], 'label' => false, 'class' => 'js-posologia', 'placeholder' => 'Posologia', 'div' => array('style' => 'width: 95%'))) ?>
							</div>
							<!-- FINAL CLASS span4 -->
							<div class="span4">
								<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.'.$key4.'.dose_diaria', array('value' => $value['dose_diaria'], 'label' => false, 'class' => 'js-dose_diaria', 'placeholder' => 'Dose diária', 'div' => array('style' => 'width: 95%'))) ?>
							</div>
							<!-- FINAL CLASS span4 -->
						</div>
						<!-- FINAL CLASS row-fluid -->
					</div>
					<!-- FINAL CLASS checkbox-canvas-->
				</div>
				<!-- FINAL CLASS inputs-config -->
				<?php 
					$e++; 
				} //FINAL FOREACH $this->data['FichaAssistencialResposta']['campo_livre'][$questao['codigo']]
			} else { ?>
			<div class="inputs-config span12 hide" style="margin-left: 0; margin-right: 1%; display: block;">
				<div class="checkbox-canvas">
					<div class="row-fluid">
						<div class="span12">
							<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.doenca', array('label' => 'CID10', 
										'class' => 'js-cid-10', 
										'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
										'div' => 'control-group input text width-full padding-left-10', 
										'required' => $required, 
										'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span>')
								); 
							?>
						</div>
						<!-- FINAL CLASS span12-->
					</div>
					<!-- FINAL CLASS row-fluid -->
					<div class="row-fluid pull-left padding-left-10 margin-top-5">
						<div class="span4">
							<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.farmaco', array('label' => false, 
										 'placeholder' => 'Fármaco', 
										 'class' => 'js-farmaco', 
										 'div' => array('style' => 'width: 95%')
										 )
								) 
							?>
						</div>
						<!--FINAL CLASS span4 -->
						<div class="span4">
							<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.posologia', array('label' => false, 
													 'placeholder' => 'Posologia', 
													 'class' => 'js-posologia', 
													 'div' => array('style' => 'width: 95%')
													 )
								) 
							?>
						</div>
						<!-- FINAL CLASS span4-->
						<div class="span4">
							<?php 
								echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.0.dose_diaria', array('label' => false, 
													   'placeholder' => 'Dose diária', 
													   'div' => array('style' => 'width: 95%')
													   )
													  ) 
							?>
						</div>
						<!-- FINAL CLASS span4 -->
					</div>
					<!-- FINAL CLASS row-fluid -->
				</div>
				<!-- FINAL CLASS checkbox-canvas-->
			</div>
			<!-- FINAL CLASS inputs-config -->
			<?php 
				} 
			}//FINAL SE $questao['multiplas_cids_ativo'] 
			?>
			<div class="hide js-memory">		
				<div class="inputs-config hide span12" style="margin-left: 0; margin-right: 1%">
					<div class="checkbox-canvas">
						<div class="row-fluid">
							<div class="span12">
								<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.doenca', array('disabled' => true, 'class' => 'js-cid-10', 'label' => 'CID10', 'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 'div' => 'control-group input text width-full padding-left-10', 'required' => $required, 'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span style="margin-top: -7px">')); ?>
								</div>	
							</div>
							<div class="row-fluid pull-left padding-left-10 margin-top-5">
								<div class="span4">
									<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.farmaco', array('disabled' => true, 'label' => false, 'class' => 'js-farmaco', 'placeholder' => 'Fármaco', 'div' => array('style' => 'width: 95%'))) ?>
								</div>
								<div class="span4">
									<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.posologia', array('disabled' => true, 'label' => false, 'class' => 'js-posologia', 'placeholder' => 'Posologia', 'div' => array('style' => 'width: 95%'))) ?>
								</div>
								<div class="span4">
									<?php echo $this->BForm->input('FichaAssistencialResposta.cid10.'.$questao['codigo'].'.xx.dose_diaria', array('disabled' => true, 'label' => false, 'placeholder' => 'Dose diária', 'div' => array('style' => 'width: 95%'))) ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- fim modulo add multiplas doencas -->

			<!-- FINALIZA O CANVAS CRIADO PARA AGRUPAR AS SUBQUESTOES -->	
			<?php if(!empty($questao['FichaAssistencialSubQuest'])  || $questao['multiplas_cids_ativo']) { ?>
		</div>
		<?php } ?>
		<!-- FIM FINALIZA O CANVAS CRIADO PARA AGRUPAR AS SUBQUESTOES-->

		<?php }

						 //FINAL FOREACH $grupoQuestao['FichaAssistencialQuestao']?>

						 <!-- FIM MONTA AS QUESTOES -->
						</div>
						<?=$final_accordion_body?>
						<!-- FINAL accordion -->
					</div>
				</div>
				<hr <?=$gestacional_preventivos?>>
				<?php }// FINAL FOREACH $questoes ?>		

				<div class="bordered">	
					<div class="row-fluid inline">	
						<h5 class="text-center">ATESTADO MÉDICO</h5>
						<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
							<div class="checkbox-canvas if-booleano">
								<?php
								// se booleano deixar apenas as respostas "sim e "não" disponiveis
								$questao['conteudo'] = json_encode(array(1 => 'Sim', 0 => 'Não'));
								$default = 0;

								if(isset($dados_atestado['exibir_ficha_assistencial']) && $dados_atestado['exibir_ficha_assistencial'] == 1){
									$default = $dados_atestado['exibir_ficha_assistencial'];
								} 

								echo $this->BForm->input('FichaAssistencial.AtestadoMedico.exibir_ficha_assistencial',
									array(	'type' => 'radio', 
										'options' => (array)json_decode($questao['conteudo']), 
										'default' => $default, 
										'data-open' => $openMenu, 
										'hiddenField' => false, 
										'legend' => false, 
										'required' => $required, 
										'data-option' => $questao['opcao_exibe_label'])
								);
								?>
								<div class="div-oculta hide">
									<div class="row-fluid pull-left padding-left-10 margin-top-5">
										<i class="icon-question-sign"></i> 
										<label class="inline-block margin-right-15" for="FichaAssistencialAtestadoMedicoHabilitaAfastamentoEmHoras">
											Afastamento em horas: 
										</label> 

										<?php
											$checked  = array('checked' => false);
											$readonly = array('readonly' => false);
											$disabled = array('disabled' => false);

											$hab_afast_horas = '0';

											if(isset($dados_atestado['habilita_afastamento_em_horas']) && $dados_atestado['habilita_afastamento_em_horas'] == 1){

												$checked  = array('checked' => true);
												$readonly = array('readonly' => true);
												$disabled = array('disabled' => true);
												$hab_afast_horas = $dados_atestado['habilita_afastamento_em_horas'];

											} else if(isset($this->data['FichaAssistencial']['AtestadoMedico']['habilita_afastamento_em_horas'])){

												$hab_afast_horas = $this->data['FichaAssistencial']['AtestadoMedico']['habilita_afastamento_em_horas'];
											}

										 echo $this->BForm->checkbox('FichaAssistencial.AtestadoMedico.habilita_afastamento_em_horas', 
											array(
												'value' => "'". $hab_afast_horas . "'", 
												$checked,
												'style' => 'margin: 0')
											); 
										?>
									</div>
									<div>&nbsp;</div>
									<div class="row-fluid pull-left padding-left-10 margin-top-5">	
										<label>Período de afastamento:</label>

										<?php 

										$data_afastamento_periodo = array('value' => date('d/m/Y'));

										if(isset($dados_atestado['data_afastamento_periodo']) && !empty($dados_atestado['data_afastamento_periodo'])){
											$data_afastamento_periodo = array('value' => $dados_atestado['data_afastamento_periodo']);
										}

										?>

										<div class="span3">
											<?php echo $this->BForm->input('FichaAssistencial.AtestadoMedico.data_afastamento_periodo', 
												array('label' => false, 
													 $data_afastamento_periodo,
													'before' => 'De: ', 
													'place-holder' => 'Afastamento', 
													'type' => 'text', 
													'class' => 'datepickerjs date input-small form-control', 
													'multiple')
											); 
											?>
										</div>
										<!-- FINAL CLASS span3-->
										<div class="span3">
											<?php 
												$data_retorno_periodo = $data_afastamento_periodo;

												if(isset($dados_atestado['data_retorno_periodo']) && !empty($dados_atestado['data_retorno_periodo'])){

													$data_retorno_periodo = array('value' => $dados_atestado['data_retorno_periodo']);
												}
											?>
											<?php echo $this->BForm->input('FichaAssistencial.AtestadoMedico.data_retorno_periodo', 
												array('label' => false, 
													'before' => 'Até: ', 
													'place-holder' => 'Retorno', 
													'type' => 'text', 
													'class' => 'datepickerjs date input-small form-control', 
													$readonly, 
													$data_retorno_periodo, 
													'multiple'
												)
											); 
											?>
										</div>
										<!-- FINAL CLASS span3-->
										<div class="span3">
											<?php 
												$afastamento_em_dias = array();

												if(isset($dados_atestado['afastamento_em_dias']) && !empty($dados_atestado['afastamento_em_dias'])){

													$afastamento_em_dias = array('value' => $dados_atestado['afastamento_em_dias']);
												}
											?>


											<?php echo $this->Form->input('FichaAssistencial.AtestadoMedico.afastamento_em_dias', 
												array(	'label' => false, 
													'class' => 'form-control span3', 
													$disabled, 
													$afastamento_em_dias, 
													'before' => 'Dias afastado: ')
											); 
											?>
										</div>
										<!-- FINAL CLASS span3-->
									</div>
									<!-- FINAL CLASS row-fluid -->
									<div class="row-fluid pull-left padding-left-10 margin-top-5">
										<label>Período de horas:</label>
										<div class="span3">

											<?php 
												$hora_afastamento = array();

												if(isset($dados_atestado['hora_afastamento']) && !empty($dados_atestado['hora_afastamento'])){

													$hora_afastamento = array('value' => $dados_atestado['hora_afastamento']);
												}
											?>

											<?php echo $this->Form->input('FichaAssistencial.AtestadoMedico.hora_afastamento',
												array('label' => false, 
													'before' => 'De: ',  
													'type' => 'text', 
													$hora_afastamento,
													'class' => 'hora form-control', 
													'multiple', 
													'style' => 'width: 40px')
											); 
											?>
										</div>
										<!-- FINAL CLASS span3 -->
										<div class="span3">
											<?php 
												$hora_retorno = array();

												if(isset($dados_atestado['hora_retorno']) && !empty($dados_atestado['hora_retorno'])){

													$hora_retorno = array('value' => $dados_atestado['hora_retorno']);
												}
											?>
											<?php echo $this->Form->input('FichaAssistencial.AtestadoMedico.hora_retorno', 
												array('label' => false, 
													'before' => 'Até: ', 
													'type' => 'text', 
													$hora_retorno,
													'class' => 'hora form-control', 
													'multiple', 
													'style' => 'width: 40px')
											); 
											?>
										</div>
										<!-- FINAL CLASS span3 -->
										<div class="span3">

											<?php 
												$afastamento_em_horas = array();

												if(isset($dados_atestado['afastamento_em_horas']) && !empty($dados_atestado['afastamento_em_horas'])){

													$afastamento_em_horas = array('value' => $dados_atestado['afastamento_em_horas']);
												}
											?>
											<?php echo $this->Form->input('FichaAssistencial.AtestadoMedico.afastamento_em_horas', 
												array('label' => false, 
													'class' => 'form-control span3', 
													$afastamento_em_horas, 
													'before' => 'Horas afastado: ')
											); 
											?>
										</div>
										<!-- FINAL CLASS span3 -->
									</div>
									<!-- FINAL CLASS row-fluid -->
									<div>&nbsp;</div>
									<div class="row-fluid pull-left padding-left-10 margin-top-5">
										<?php 
											$motivo = array();

											if(isset($dados_atestado['codigo_motivo_licenca']) && !empty($dados_atestado['codigo_motivo_licenca'])){

												$motivo = array('selected' => $dados_atestado['codigo_motivo_licenca']);
											}
										?>
										<?php echo $this->BForm->input('FichaAssistencial.AtestadoMedico.codigo_motivo_licenca', 
											array('class' => 'input-xlarge', 
												'label' => 'Motivo da Licença', 
												'options' => $MotivoAfastamento, 
												$motivo, 
												'empty' =>'Selecione uma opção')
										); ?>
									</div>
									<!-- FINAL CLASS row-fluid -->
									<div class="row-fluid pull-left padding-left-10 margin-top-5">

										<?php 
											$esocial = array();

											if(isset($dados_atestado['codigo_motivo_esocial']) && !empty($dados_atestado['codigo_motivo_esocial'])){
												$esocial = array('selected' => $dados_atestado['codigo_motivo_esocial']);
											}
										?>

										<?php echo $this->BForm->input('FichaAssistencial.AtestadoMedico.codigo_motivo_esocial', 
											array('class' => 'input-xxlarge', 
												'label' => 'Motivo da Licença (Tabela 18 - eSocial)', 
												'options' => $motivo_afastamento_esocial, 
												$esocial,
												'empty' => 'Selecione')
										); ?>
									</div>
									<!-- FINAL CLASS row-fluid -->
									<div class="row-fluid pull-left padding-left-10 margin-top-5">

										<?php 
											$restricao	= array();

											if(isset($dados_atestado['restricao']) && !empty($dados_atestado['restricao'])){
												$restricao = array('value' => $dados_atestado['restricao']);
											}
										?>

										<?php echo $this->BForm->input('FichaAssistencial.AtestadoMedico.restricao', 
											array('class' => 'input-xxlarge', 
												'label' => 'Restrição para o retorno',
												$restricao
											)
										); 
										?>

										<?php echo $this->BForm->hidden('FichaAssistencial.codigo_atestado', array('value' => $this->data['FichaAssistencial']['codigo_atestado'])); ?>
									</div>
									<!-- FINAL CLASS row-fluid -->
									<div class="row-fluid pull-left padding-left-10 margin-top-5">
										<div class="js-encapsulado-cid10">

											<?php 
												if(!empty($this->data['FichaAssistencial']['AtestadoMedico']['cid10'])) {
													$i_cid10 = count(($this->data['FichaAssistencial']['AtestadoMedico']['cid10']))-1;
														
													$cid10 = 0; 
													foreach ($this->data['FichaAssistencial']['AtestadoMedico']['cid10'] as $key_cid10 => $value_cid10) {
													?>
											<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
												<div class="checkbox-canvas">
													<div class="row-fluid">
														<div class="span12">
															<?php 
															echo $this->BForm->input('FichaAssistencial.AtestadoMedico.cid10.'.$key_cid10.'.doenca', 
																array('label' => 'CID10', 
																	'class' => 'js-cid10', 
																	'value' => $value_cid10['doenca'], 
																	'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
																	'div' => false, 
																	'required' => $required, 
																	'after' => '<span style="margin-top: -7px" 
																						 class="btn btn-default 
																						 
																						 '. (($key_cid10 == $i_cid10 ) ? 
																						'js-add-cid10 ' : 'js-remove-cid10 ' ). '
																						 pointer pull-right" 
																						 data-toggle="tooltip" 
																					 	 title="
																					 	 '. (($key_cid10 == $i_cid10 ) ? 
																					'Adicionar novo doeça' : 'Remover doença' ). '
																					">
																					<i class="'.(($key_cid10 == $i_cid10 ) ? 'icon-plus' : 'icon-minus').'"></i>
																					</span>')


															); 
															?>
														</div>
														<!-- FINAL span12 -->
													</div>
													<!-- FINAL row-fluid -->
												</div>
												<!-- FINAL checkbox-canvas -->
											</div>
											<!-- FINAL inputs-config -->
											<?php
												$cid10++;
												}//FINAL FOREACH $this->data['FichaAssistencial']['AtestadoMedico']['cid10']
											}else{
												?>
												<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
													<div class="checkbox-canvas">
														<div class="row-fluid">
															<div class="span12">
																<?php 
																echo $this->BForm->input('FichaAssistencial.AtestadoMedico.cid10.0.doenca', 
																	array('label' => 'CID10', 
																		'class' => 'js-cid10', 
																		'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
																		'div' => false, 
																		'required' => $required, 
																		'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid10 pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span>')
																); 
																?>
															</div>
															<!-- FINAL span12 -->
														</div>
														<!-- FINAL row-fluid -->
													</div>
													<!-- FINAL checkbox-canvas -->
												</div>
												<!-- FINAL inputs-config -->
											<?php } ?>
											<div class="hide js-memory-cid10">		
												<div class="inputs-config hide span12" style="margin-left: 0; margin-right: 1%">
													<div class="checkbox-canvas">
														<div class="row-fluid">
															<div class="span12">
																<?php echo $this->BForm->input('FichaAssistencial.AtestadoMedico.cid10.xx.doenca', 
																	array('disabled' => true, 
																		'label' => 'CID10', 
																		'class' => 'js-cid10', 
																		'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
																		'div' => false, 
																		'required' => $required, 
																		'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid10 pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span style="margin-top: -7px">')
																); 
																?>
															</div>
															<!-- FINAL span12 -->
														</div>
														<!-- FINAL row-fluid -->
													</div>
													<!-- FINAL checkbox-canvas-->
												</div>
												<!-- FINAL inputs-config-->
											</div>
											<!-- FINAL js-memory -->
										</div>
										<!-- FINAL js-encapsulado-cid10-->
									</div>
								</div>
								<!-- FINAL CLASS div-oculta -->
							</div>
							<!-- FINAL checkbox-canvas -->
						</div>
						<!-- FINAL inputs-config-->
					</div>
				</div>
				<!-- FINAL bordered-->
			</div>	
		</div>	

		<div class="form-actions">
			<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
			<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
		</div>


		<?php echo $this->Javascript->codeBlock("    
			$(document).ready(function(){
				setup_mascaras();
				setup_time();
			});	
			$(window).load(function() {
				$('.control-group.input.select label').each(function(index, val) {
					$(this).removeAttr('for');
				});
				
				$('.control-group.input.radio').each(function(index, val){
					if($(this).hasClass('required')) {
						$(this).parent().children('label').prepend('<span class=\"color-red\">* </span>');
					}
				});
				$('.control-group.input.text, .control-group.input.select, .control-group.input.time').each(function(index, val){
					if($(this).hasClass('required')) {
						$(this).children('label').prepend('<span class=\"color-red\">* </span>');
					}
				});
			});
			"); ?>

<script type="text/javascript">
	$(document).ready(function() {

	    jQuery("input[type='submit']").on('click', function(){
	        jQuery(this).addClass("btn-disable");
	        jQuery(this).attr("value", "Carregando, Aguarde..");
        });

		function validaRadioChecked(this_attr){

			if(this_attr.attr('data-open')) {
				if(this_attr.val() == this_attr.attr('data-open')) {
					this_attr.parents('.subgroup-question').find('.inputs-config.hide').show();
				} else {
					this_attr.parents('.subgroup-question').find('.inputs-config.hide').hide();
				}
				if(this_attr.attr('data-option')) {
					if(this_attr.attr('data-option') == this_attr.val()) {
						this_attr.parents('.checkbox-canvas').find('.js-label.hide').show();
					} else {
						this_attr.parents('.checkbox-canvas').find('.js-label.hide').hide();
					}
				}
			} else {

				if(!isNaN(parseInt(this_attr.val())) &&  this_attr.attr('suboption') != 1) {	
					if(parseInt(this_attr.val()) > 0) {
						this_attr.parents('.subgroup-question').find('.inputs-config.hides').show();
						this_attr.parents('.if-booleano').find('.div-oculta').show();
					} else {
						this_attr.parents('.subgroup-question').find('.inputs-config.hides').hide();
						this_attr.parents('.if-booleano').find('.div-oculta').hide();
					}
				}
				if(this_attr.attr('data-option')) {
					if(this_attr.attr('data-option') == this_attr.val()) {
						this_attr.parents('.checkbox-canvas').find('.js-label.hide').show();
					} else {
						this_attr.parents('.checkbox-canvas').find('.js-label.hide').hide();
					}
				}
			}
		}

		/**
		 * funcao para calcular o imc
		 * @return {[type]} [description]
		 */
		function calcula_imc(){
		 	var peso_k = $("#FichaAssistencialPesoKg").val();
		 	var peso_g = $("#FichaAssistencialPesoGr").val();

		 	var altura_m = $("#FichaAssistencialAlturaMt").val();
		 	var altura_cm = $("#FichaAssistencialAlturaCm").val();

		 	var peso = peso_k+"."+peso_g;
		 	var altura = altura_m+"."+altura_cm;

		 	var imc = 0.0;
		 	var label = 'Não informado!';
		 	var clas = 'center label_status bad';
			// console.log("entro calcula");

			if(peso != "" && peso != '.' && peso != '0.' && peso != '0.0' 
				&& altura != '' && altura != '.' && altura != '0.' && altura != '0.0') {

				// console.log(peso+"--"+altura);

			imc = (peso / (altura * altura));

				// console.log(imc);
				if((imc < 18.5)){
					label = "Magro ou baixo peso";
					clas = 'center label_status good';
				}
				else if((imc >= 18.5) && (imc < 24.99)){
					label = "Normal ou eutrófico";
					clas = 'center label_status good';
				}
				else if((imc >= 25) && (imc < 29.99)){
					label = "Sobrepeso ou pré-obeso";
					clas = 'center label_status bad';
				}
				else if((imc >= 30) && (imc < 34.99)){
					label = "Obesidade";
					clas = 'center label_status bad';
				}
				else if((imc >= 35) && (imc < 39.99)){
					label = "Obesidade";
					clas = 'center label_status bad';
				}
				else if((imc >= 40)){
					label = "Obesidade (grave)";
					clas = 'center label_status bad';
				}

				// console.log(label);
			}//fim if

			//seta os valores para os campos
			$("#FichaAssistencialImc").val(imc.toFixed(1));
			$("#userImcMsg").text(label);
			$("#userImcMsg").removeClass();
			$("#userImcMsg").addClass(clas);

		}//fim calcula_imc

		$('.calc_imc').on('change', function(){
			calcula_imc();
		});

		$('.if-booleano').find('input[type="radio"]:checked').each(function(){
			validaRadioChecked($(this));
		});

		$('.if-booleano').find('input[type="radio"]').click(function(event) {
			validaRadioChecked($(this));
		});

		var cLine = '<?php echo ((isset($e))? ($e + 1) : 1) ?>';
		
		//console.log(cLine);
		$('body').on('click', '.js-add-cid', function() {
			var html = $(this).parents('.js-encapsulado')
							  .find('.js-memory')
							  .html()
							  .replace(/xx/g, cLine)
							  .replace(/Xx/g, cLine)
							  .replace(/disabled="disabled"/g, '');

			$(this).parents('.js-encapsulado')
				   .append(html)
				   .find('.inputs-config.hide')
				   .show();

			$(this).removeClass('js-add-cid')
				   .addClass('js-remove-cid')
				   .attr('data-original-title', 'Remover doença')
				   .children('i')
				   .removeClass('icon-plus')
				   .addClass('icon-minus');

			$('[data-toggle="tooltip"]').tooltip();
			cLine++;
		});

		$('body').on('click', '.js-remove-cid', function() {
			$(this).parents('.inputs-config').remove();
		});

		var i_farmaco = '<?php echo ((isset($farmaco))? ($farmaco + 1) : 1) ?>';
		$('body').on('click', '.js-add-farmaco', function() {

				var html = $(this).parents('.js-encapsulado-farmaco')
								  .find('.js-memory-farmaco')
								  .html()
								  .replace(/xx/g, i_farmaco)
								  .replace(/Xx/g, i_farmaco)
								  .replace(/disabled="disabled"/g, '');
				
				$(this).parents('.js-encapsulado-farmaco')
					   .append(html)
					   .find('.inputs-config.hide')
					   .show();

				$(this).removeClass('js-add-farmaco')
					   .addClass('js-remove-farmaco')
					   .attr('data-original-title', 'Remover doença')
					   .children('i')
					   .removeClass('icon-plus')
					   .addClass('icon-minus');

				$('[data-toggle="tooltip"]').tooltip();
				i_farmaco++;
		});//FINAL CLICK js-add-farmaco


		$('body').on('click', '.js-remove-farmaco', function() {
			$(this).closest('.inputs-config').remove();
		});//FINAL CLICK js-remove-farmaco
		
		var i_cid10 = '<?php echo ((isset($cid10))? ($cid10 + 1) : 1) ?>';
		$('body').on('click', '.js-add-cid10', function() {

			var html = $(this).parents('.js-encapsulado-cid10')
			.find('.js-memory-cid10')
			.html()
			.replace(/xx/g, i_cid10)
			.replace(/Xx/g, i_cid10)
			.replace(/disabled="disabled"/g, '');
			
			$(this).parents('.js-encapsulado-cid10')
			.append(html)
			.find('.inputs-config.hide')
			.show();

			$(this).removeClass('js-add-cid10')
			.addClass('js-remove-cid10')
			.attr('data-original-title', 'Remover doença')
			.children('i')
			.removeClass('icon-plus')
			.addClass('icon-minus');

			$('[data-toggle="tooltip"]').tooltip();
			i_cid10++;
		});//FINAL CLICK js-add-cid10

		$('body').on('click', '.js-remove-cid10', function() {
			$(this).closest('.inputs-config').remove();
		});//FINAL CLICK js-remove-cid10
		// modulo farmaco
		var timer;
		$("body").on('keyup', '.js-farmaco, .js-farm', function() {
			var este = $(this);
			var string = this.value;
			if(string != '') {
				este.parent().css('position', 'relative');
				$('.loader-gif').remove();
				este.parent().append(' <img src="'+baseUrl+'img/default.gif" style="margin-top: -10px;" class="loader-gif">');
				$('.seleciona-farmaco').remove();
				clearTimeout(timer); 
				timer = setTimeout(function() {
					$.ajax({
						url: baseUrl + 'medicamentos/carregaMedicamentosParaAjax/',
						type: 'POST',
						dataType: 'json',
						data: {string: string},
					})
					.done(function(response) {
						if(response) {
							var canvas = $('<div>', {class: 'seleciona-farmaco'}).html(response);
							este.parent().append(canvas);
						}
					})
					.always(function() {
						$('.loader-gif').remove();
					});
				}, 500);
			} else {
				$('.seleciona-farmaco').remove();
				$('.loader-gif').remove();
			}
		});

		$('body').on('click', '.js-click', function() {
			$(this).parents('.checkbox-canvas').find('.js-farmaco').val($(this).find('td:first-child').text());
			$(this).parents('.checkbox-canvas').find('.js-posologia').val($(this).find('td:last-child').text());

			$(this).closest('.checkbox-canvas').find('.js-farm').val($(this).find('td:first-child').text());
			$(this).closest('.checkbox-canvas').find('.js-poso').val($(this).find('td:last-child').text());

			$('.seleciona-farmaco').remove();
		});

		$('body').click(function(event) {
			$('.seleciona-farmaco').remove();
		});
		// ===============

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
		});

		$('body').on('click', '.js-cid-click', function() {
			$(this).closest('.checkbox-canvas').find('.js-cid10').val($(this).find('td:first-child').text());
			$(this).parents('.checkbox-canvas').find('.js-cid-10').val($(this).find('td:first-child').text());
			$('.seleciona-cid-10').remove();
		});

		$('body').click(function(event) {
			$('.seleciona-cid-10').remove();
		});
		// ===============

		$('h5').on('click', function(){
			if($(this).find('i').hasClass('icon-plus')){
				$(this).find('i').toggleClass('icon-minus');
			}else{
				$(this).find('i').toggleClass('icon-plus');
			}
		});//FINAL CLICK h5
	});//FINAL document.ready

	function checa_valor() {
		
		var data_afastamento_periodo = $('#FichaAssistencialAtestadoMedicoDataAfastamentoPeriodo');
		var hora_afastamento 		 = $('#FichaAssistencialAtestadoMedicoHoraAfastamento').val();
		var hora_retorno 			 = $('#FichaAssistencialAtestadoMedicoHoraRetorno').val();
		calculo_de_dias(data_afastamento_periodo.val(), data_afastamento_periodo.val(), hora_afastamento, hora_retorno);

		$('#FichaAssistencialAtestadoMedicoAfastamentoEmDias').attr('disabled', true);
		$('#FichaAssistencialAtestadoMedicoDataRetornoPeriodo').attr('readonly', true).val(data_afastamento_periodo.val());
		$('#FichaAssistencialAtestadoMedicoAfastamentoEmDias').val(0);
		data_afastamento_periodo.focusout(function(event) {
			insere_data_ini_no_fim();
		});
	}//FINAL FUNCTION checa_valor

	function calculo_de_dias(data1, data2, hora1, hora2) {
		if(data1 != undefined && data1 != '' && data2 != undefined && data2 != '') {	

			data1 = data1.split('/');
			data2 = data2.split('/');
			data1 = new Date(data1[1]+'/'+data1[0]+'/'+data1[2]);
			data2 = new Date(data2[1]+'/'+data2[0]+'/'+data2[2]);

			if(data1 > data2) {
				$('#FichaAssistencialAtestadoMedicoAfastamentoEmDias').val('');
				return 0;
			}

			var timeDiff = Math.abs(data2.getTime() - data1.getTime());
			var diff = Math.ceil(timeDiff / (1000 * 3600 * 24))+1
			$('#FichaAssistencialAtestadoMedicoAfastamentoEmDias').val(diff);

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
					$('#FichaAssistencialAtestadoMedicoAfastamentoEmHoras').val(hDif + ':' + mDif);			
				} else{
					$('#FichaAssistencialAtestadoMedicoAfastamentoEmHoras').val('');
				}
			} else {
				$('#FichaAssistencialAtestadoMedicoAfastamentoEmHoras').val('');
			}
		}
	}//FINAL FUNCTION calculo_de_dias

	function insere_data_ini_no_fim() {

		$('#FichaAssistencialAtestadoMedicoDataRetornoPeriodo').val($('#FichaAssistencialAtestadoMedicoDataAfastamentoPeriodo').val());
		$('#FichaAssistencialAtestadoMedicoAfastamentoEmDias').val(0);
	}//FINAL FUNCTION insere_data_ini_no_fim

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

			var data_afastamento_periodo 	= $('#FichaAssistencialAtestadoMedicoDataAfastamentoPeriodo').val();
			var data_retorno_periodo  		= $('#FichaAssistencialAtestadoMedicoDataRetornoPeriodo').val();
			var hora_afastamento 			= $('#FichaAssistencialAtestadoMedicoHoraAfastamento').val();
			var hora_retorno				= $('#FichaAssistencialAtestadoMedicoHoraRetorno').val();

			calculo_de_dias(data_afastamento_periodo, data_retorno_periodo, hora_afastamento, hora_retorno);

			if($('#FichaAssistencialAtestadoMedicoHabilitaAfastamentoEmHoras').is(':checked')) {
				insere_data_ini_no_fim();
			}
		}
	}).mask("99/99/9999"); //FINAL datepicker

	$(document).ready(function() {
		
		var habilita_hora = $('#FichaAssistencialAtestadoMedicoHabilitaAfastamentoEmHoras').val();
		
		var data_retorno = $('#FichaAssistencialAtestadoMedicoDataRetornoPeriodo').val();
		data_retorno = (data_retorno != '') ? data_retorno : 0;
		
		//console.log(habilita_hora);

		if(habilita_hora == 1 || habilita_hora == '1') {
			checa_valor();
			$('#FichaAssistencialAtestadoMedicoHabilitaAfastamentoEmHoras').attr('checked', 'checked');
		} else {
			if(data_retorno == 0) {
				$('#FichaAssistencialAtestadoMedicoDataRetornoPeriodo').val('<?php echo date('d/m/Y') ?>');
				
				var data_afastamento_periodo 	= $('#FichaAssistencialAtestadoMedicoDataAfastamentoPeriodo').val();
				var data_retorno_periodo 		= $('#FichaAssistencialAtestadoMedicoDataRetornoPeriodo').val();
				var hora_afastamento			= $('#FichaAssistencialAtestadoMedicoHoraAfastamento').val();
				var hora_retorno				= $('#FichaAssistencialAtestadoMedicoHoraRetorno').val();

				calculo_de_dias(data_afastamento_periodo, data_retorno_periodo, hora_afastamento, hora_retorno);
			}//FINAL SE data_retorno IGUAL A ZERO
		}//FINAL habilita_hora IGUAL A UM
		
		$('#FichaAssistencialAtestadoMedicoHabilitaAfastamentoEmHoras').click(function(event) {

			if($(this).is(':checked')) {
				$(this).val('1').attr('checked', 'checked');
				checa_valor();
			} else {
				$(this).val('0').removeAttr('checked');

				var data_afastamento_periodo 	= $('#FichaAssistencialAtestadoMedicoDataAfastamentoPeriodo'); 
				var data_retorno_periodo 		= $('#FichaAssistencialAtestadoMedicoDataRetornoPeriodo'); 
				var hora_afastamento			= $('#FichaAssistencialAtestadoMedicoHoraAfastamento').val();
				var hora_retorno				= $('#FichaAssistencialAtestadoMedicoHoraRetorno').val()

				calculo_de_dias(data_afastamento_periodo.val(), data_retorno_periodo.val(), hora_afastamento, hora_retorno);

				data_afastamento_periodo.unbind();
				$('#FichaAssistencialAtestadoMedicoAfastamentoEmDias').attr('disabled', false);
				data_retorno_periodo.attr('readonly', false);
				data_afastamento_periodo.unbind();
			}
		});//FINAL CLICK FichaAssistencialAtestadoMedicoHabilitaAfastamentoEmHoras

		$('#FichaAssistencialAtestadoMedicoHoraAfastamento').focusout(function(event) {

			var data_afastamento_periodo 	= $('#FichaAssistencialAtestadoMedicoDataAfastamentoPeriodo').val();
			var data_retorno_periodo 		= $('#FichaAssistencialAtestadoMedicoDataRetornoPeriodo').val(); 
			var hora_afastamento			= $(this).val();
			var hora_retorno				= $('#FichaAssistencialAtestadoMedicoHoraRetorno').val();

			calculo_de_dias(data_afastamento_periodo, data_retorno_periodo, hora_afastamento, hora_retorno);
		});//FINAL FOCUSOUT FichaAssistencialAtestadoMedicoHoraAfastamento

		$('#FichaAssistencialAtestadoMedicoHoraRetorno').focusout(function(event) {

			var data_afastamento_periodo 	= $('#FichaAssistencialAtestadoMedicoDataAfastamentoPeriodo').val();
			var data_retorno_periodo 		= $('#FichaAssistencialAtestadoMedicoDataRetornoPeriodo').val();
			var hora_afastamento			= $('#FichaAssistencialAtestadoMedicoHoraAfastamento').val();
			var hora_retorno				= $(this).val();

			calculo_de_dias(data_afastamento_periodo, data_retorno_periodo, hora_afastamento, hora_retorno);
		});//FINAL FOCUSOUT FichaAssistencialAtestadoMedicoHoraRetorno

		$('#FichaAssistencialAtestadoMedicoDataAfastamentoPeriodo').focusout(function(event) {

			var data_afastamento_periodo 	= $('#FichaAssistencialAtestadoMedicoDataAfastamentoPeriodo').val();
			var data_retorno_periodo 		= $('#FichaAssistencialAtestadoMedicoDataRetornoPeriodo').val();
			var hora_afastamento			= $('#FichaAssistencialAtestadoMedicoHoraAfastamento').val();
			var hora_retorno				= $(this).val();

			calculo_de_dias(data_afastamento_periodo, data_retorno_periodo, hora_afastamento, hora_retorno);
		});//FINAL FOCUSOUT FichaAssistencialAtestadoMedicoDataAfastamentoPeriodo
	});// FINAL document.ready

</script>