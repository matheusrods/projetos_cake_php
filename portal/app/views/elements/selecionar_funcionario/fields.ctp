<div class='well'>
	<div class="bordered">
		<div class='row-fluid'>	
			<h5 class="text-center">DADOS PRINCIPAIS</h5>
			<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
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
				<?php echo $this->BForm->input('setor', array('value' => $dados['Setor']['descricao'], 'label' => 'Setor:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
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
				<?php echo $this->BForm->input('cargo', array('value' => $dados['Cargo']['descricao'],'label' => 'Cargo:', 'style' => 'width: 92%; margin-bottom: 0', 'readonly' => true)) ?>
			</div>
			<div class="span3 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('tipo_exame_ocipacional', array('label' => 'Tipo do exame ocupacional:', 'style' => 'width: 92%; margin-bottom: 0', 'value' => $dados['PedidoExame']['tipo_pedido_exame'], 'readonly' => true)) ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="clear"></div>
			<hr>
			<div class="span4 no-margin-left checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('incluido_por', array('label' => 'Incluído por:', 'style' => 'width: 95%; margin-bottom: 0', 'required' => 'required')) ?>
			</div>
			<div class="span4 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('codigo_medico', array('label' => 'Médico:', 'options' => $dados['Medico'], 'empty' => ((!is_null($this->data))? null : 'Selecione'), 'style' => 'width: 95%; margin-bottom: 0', 'required' => 'required')) ?>
			</div>
			<div class="span4 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('hora_inicio_atendimento', array('label' => 'Horário de início de atendimento:', 'type' => 'time', 'style' => 'width: 31%; margin-bottom: 0')) ?>
			</div>
			<div class="row-fluid">
				<div class="span4 offset4 checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('hora_fim_atendimento', array('label' => 'Horário de finalização de atendimento:', 'type' => 'time', 'style' => 'width: 31%; margin-bottom: 0')) ?>
				</div>
			</div>
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
					<?php echo $this->BForm->input('peso_kg', array('div' => false, 'label' => 'Peso (kg):',  'style' => 'width: 40%; margin-bottom: 0', 'placeholder' => 'Quilos', 'after' => '&nbsp;k')) ?>&nbsp;
					<?php echo $this->BForm->input('peso_gr', array('label' => false, 'div' => false,  'style' => 'width: 40%; margin-bottom: 0', 'placeholder' => 'Gramas', 'after' => '&nbsp;g')) ?>
				</div>
				<div class="span4 checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('altura_mt', array('div' => false, 'label' => 'Altura (cm):',  'style' => 'width: 40%; margin-bottom: 0', 'placeholder' => 'Metros', 'after' => '&nbsp;m')) ?>&nbsp;
					<?php echo $this->BForm->input('altura_cm', array('label' => false, 'div' => false,  'style' => 'width: 40%; margin-bottom: 0', 'placeholder' => 'Centímetros', 'after' => '&nbsp;cm')) ?>
				</div>
				<div class="span4 checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('circunferencia_quadril', array('div' => false, 'label' => 'Circunferência quadril (cm):', 'style' => 'width: 95%; margin-bottom: 0')) ?>
				</div>
			</div>
		</div>
		<hr>

		<?php foreach ($questoes as $key => $grupoQuestao) { ?>
		<div class="bordered">
			<div class='row-fluid inline'>	
				<!-- TITULO DO GRUPO DE QUESTOES -->
				<h5 class="text-center"><?php echo $grupoQuestao['FichaClinicaGrupoQuestao']['descricao'] ?></h5>
				<!-- FIM -->

				<!-- MONTA AS QUESTOES -->
				<?php foreach ($grupoQuestao['FichaClinicaQuestao'] as $key2 => $questao) {
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
					<?php if(!empty($questao['FichaClinicaSubQuestao']) || $questao['multiplas_cids_ativo']) { ?>
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
									echo $this->BForm->input('FichaClinicaResposta.'.$questao['codigo'].'_resposta', array('label' => $label, 'style' => 'width: 95%; margin-bottom: 0; margin-top: -6px', 'div' => 'control-group input text width-full padding-left-10', 'required' => $required));
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
									echo $this->BForm->label('FichaClinicaResposta.'.$questao['codigo'].'_resposta', $label, array('for' => $questao['codigo']));

									// cria o input
									echo $this->BForm->input('FichaClinicaResposta.'.$questao['codigo'].'_resposta', array('type' => 'radio', 'options' => (array)json_decode($questao['conteudo']), 'default' => $default, 'data-open' => $openMenu, 'hiddenField' => false, 'legend' => false, 'required' => $required, 'data-option' => $questao['opcao_exibe_label']));

								// cria o campo livre caso exista
									if(!empty($questao['parentesco_ativo'])) {
										echo $this->BForm->input('FichaClinicaResposta.parentesco.'.$questao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos'=> 'Irmãos'), 'empty' => 'Parentesco', 'class' => 'adjust-parentesco', 'div' => false, 'label' => false));
									} elseif(!empty($questao['campo_livre_label'])) {
										echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$questao['codigo'], array('type' => 'text', 'style' => 'width: 95%', 'label' => false, 'placeholder' => $questao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;', 'div' => 'js-label '.((!empty($questao['opcao_exibe_label']))? 'hide' : '')));
									}
									break;

								//CASO O CAMPO SEJA DO TIPO CHECKBOX:
									case 'CHECKBOX':

								// cria a label
									echo $this->BForm->label('FichaClinicaResposta.'.$questao['codigo'].'_resposta', $label, array('for' => $questao['codigo']));

								// cria o input
									echo $this->BForm->input('FichaClinicaResposta.'.$questao['codigo'].'_resposta', array('type' => 'select', 'multiple' => 'checkbox', 'options' => (array)json_decode($questao['conteudo']), 'label' => false, 'id' => false, 'hiddenField' => false,'required' => $required));

								// cria o campo livre caso exista
									if(!empty($questao['parentesco_ativo'])) {
										echo $this->BForm->input('FichaClinicaResposta.parentesco.'.$questao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'empty' => 'Parentesco', 'class' => 'adjust-parentesco', 'div' => false, 'label' => false));
									} elseif(!empty($questao['campo_livre_label'])) {
										echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$questao['codigo'], array('type' => 'text', 'style' => 'width: 95%', 'label' => false, 'placeholder' => $questao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;'));
									}
									break;
								} ?>

								<!-- Monta o módulo fármaco -->
								<?php if($questao['farmaco_ativo']) { ?>
								<?php echo $this->BForm->hidden('FichaClinicaResposta.'.$questao['codigo'].'_resposta', array('value' => 1)); ?>
								<div class="row-fluid pull-left padding-left-10 margin-top-5">
									<div class="span4">
										<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$questao['codigo'].'.farmaco', array('label' => false, 'placeholder' => 'Fármaco', 'class' => 'js-farmaco', 'div' => array('style' => 'width: 95%'))) ?>
									</div>
									<div class="span4">
										<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$questao['codigo'].'.posologia', array('label' => false, 'placeholder' => 'Posologia', 'class' => 'js-posologia', 'div' => array('style' => 'width: 95%'))) ?>
									</div>
									<div class="span4">
										<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$questao['codigo'].'.dose_diaria', array('label' => false, 'placeholder' => 'Dose diária', 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
									</div>
								</div>
								<?php } ?>
								<!-- fim modulo farmaco -->

							</div>	
						</div>	

						<!-- MONTA AS SUBQUESTOES -->
						<?php if(!empty($questao['FichaClinicaSubQuestao'])) { ?>
						<div class="clear"></div>

						<?php foreach ($questao['FichaClinicaSubQuestao'] as $key3 => $subquestao) { 

						//se houver quebra de linha, aplique
							if(!empty($subquestao['quebra_linha'])) {
								echo '<div class="clear"></div>';
							}

						// parametriza o nenu escondido
							$hide = '';
							if($questao['tipo'] == 'BOOLEANO') {
								$hide = 'hide';
							} elseif($questao['tipo'] == 'RADIO' && $questao['opcao_abre_menu_escondido']) {
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
										echo $this->BForm->input('FichaClinicaResposta.'.$subquestao['codigo'].'_resposta', array('label' => $label, 'style' => 'width: 95%; margin-bottom: 0; margin-top: -6px', 'div' => 'control-group input text width-full padding-left-10', 'required' => $required));
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
										echo $this->BForm->label('FichaClinicaResposta.'.$subquestao['codigo'].'_resposta', $label, array('for' => $subquestao['codigo']));

										// cria o input
										echo $this->BForm->input('FichaClinicaResposta.'.$subquestao['codigo'].'_resposta', array('type' => 'radio', 'options' => (array)json_decode($subquestao['conteudo']), 'default' => $default, 'data-open' => $openMenu, 'hiddenField' => false, 'legend' => false, 'required' => $required, 'suboption' => 1, 'data-option' => $subquestao['opcao_exibe_label']));

										// cria o campo livre caso exista
										if(!empty($subquestao['parentesco_ativo'])) {
											echo $this->BForm->input('FichaClinicaResposta.parentesco.'.$subquestao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'default' => $default, 'empty' => 'Parentesco', 'class' => 'adjust-parentesco', 'div' => false, 'label' => false));
										} elseif(!empty($subquestao['campo_livre_label'])) {
											echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$subquestao['codigo'], array('style' => 'width: 95%', 'label' => false, 'placeholder' => $subquestao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;', 'div' => 'js-label '.((!empty($subquestao['opcao_exibe_label']))? 'hide' : '')));
										}
										break;

										//CASO O CAMPO SEJA DO TIPO CHECKBOX:
										case 'CHECKBOX':

										// cria a label
										echo $this->BForm->label('FichaClinicaResposta.'.$subquestao['codigo'].'_resposta', $label, array('for' => $subquestao['codigo']));

										// cria o input
										echo $this->BForm->input('FichaClinicaResposta.'.$subquestao['codigo'].'_resposta', array('type' => 'select', 'multiple' => 'checkbox', 'options' => (array)json_decode($subquestao['conteudo']), 'label' => false, 'id' => false, 'hiddenField' => false, 'required' => $required));

									// cria o campo livre caso exista
										if(!empty($subquestao['parentesco_ativo'])) {
											echo $this->BForm->input('FichaClinicaResposta.parentesco.'.$subquestao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'empty' => 'Parentesco', 'class' => 'adjust-parentesco', 'div' => false, 'label' => false));
										} elseif(!empty($subquestao['campo_livre_label'])) {
											echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$subquestao['codigo'], array('type' => 'text', 'style' => 'width: 95%', 'label' => false, 'placeholder' => $subquestao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;'));
										}
										break;

									} ?>
									<!-- modulo farmaco -->
									<?php if($subquestao['farmaco_ativo']) { ?>
									<?php echo $this->BForm->hidden('FichaClinicaResposta.'.$subquestao['codigo'].'_resposta', array('value' => 1)); ?>
									<div class="row-fluid pull-left padding-left-10 margin-top-5">
										<div class="span4">
											<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$subquestao['codigo'].'.farmaco', array('label' => false, 'placeholder' => 'Fármaco', 'class' => 'js-farmaco', 'div' => array('style' => 'width: 95%'))) ?>
										</div>
										<div class="span4">
											<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$subquestao['codigo'].'.posologia', array('label' => false, 'placeholder' => 'Posologia', 'class' => 'js-posologia', 'div' => array('style' => 'width: 95%'))) ?>
										</div>
										<div class="span4">
											<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$subquestao['codigo'].'.dose_diaria', array('label' => false, 'placeholder' => 'Dose diária', 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
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
								<?php if($questao['multiplas_cids_ativo']) { ?>
								<div class="inputs-config span12 hide" style="margin-left: 0; margin-right: 1%">
									<div class="checkbox-canvas">
										<div class="row-fluid">
											<div class="span12">
												<?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.0.doenca', array('label' => 'CID10', 'class' => 'js-cid-10', 'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 'div' => 'control-group input text width-full padding-left-10', 'required' => $required, 'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span>')); ?>
											</div>	
										</div>
										<div class="row-fluid pull-left padding-left-10 margin-top-5">
											<div class="span4">
												<?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.0.farmaco', array('label' => false, 'placeholder' => 'Fármaco', 'class' => 'js-farmaco', 'div' => array('style' => 'width: 95%'))) ?>
											</div>
											<div class="span4">
												<?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.0.posologia', array('label' => false, 'placeholder' => 'Posologia', 'class' => 'js-posologia', 'div' => array('style' => 'width: 95%'))) ?>
											</div>
											<div class="span4">
												<?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.0.dose_diaria', array('label' => false, 'placeholder' => 'Dose diária', 'div' => array('style' => 'width: 95%'))) ?>
											</div>
										</div>
									</div>
								</div>
								<?php } ?>
								<!-- fim modulo add multiplas doencas -->


								<div class="hide js-memory">		
									<div class="inputs-config hide span12" style="margin-left: 0; margin-right: 1%">
										<div class="checkbox-canvas">
											<div class="row-fluid">
												<div class="span12">
													<?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.xx.doenca', array('disabled' => true, 'label' => 'CID10', 'class' => 'js-cid-10', 'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 'div' => 'control-group input text width-full padding-left-10', 'required' => $required, 'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span style="margin-top: -7px">')); ?>
												</div>	
											</div>
											<div class="row-fluid pull-left padding-left-10 margin-top-5">
												<div class="span4">
													<?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.xx.farmaco', array('disabled' => true, 'label' => false, 'class' => 'js-farmaco', 'placeholder' => 'Fármaco', 'div' => array('style' => 'width: 95%'))) ?>
												</div>
												<div class="span4">
													<?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.xx.posologia', array('disabled' => true, 'label' => false, 'class' => 'js-posologia', 'placeholder' => 'Posologia', 'div' => array('style' => 'width: 95%'))) ?>
												</div>
												<div class="span4">
													<?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.xx.dose_diaria', array('disabled' => true, 'label' => false, 'placeholder' => 'Dose diária', 'div' => array('style' => 'width: 95%'))) ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- FINALIZA O CANVAS CRIADO PARA AGRUPAR AS SUBQUESTOES -->	
							<?php if(!empty($questao['FichaClinicaSubQuestao'])  || $questao['multiplas_cids_ativo']) { ?>
						</div>
						<?php } ?>
						<!-- FIM FINALIZA O CANVAS CRIADO PARA AGRUPAR AS SUBQUESTOES-->

						<?php } ?>
						<!-- FIM MONTA AS QUESTOES -->
					</div>
				</div>
				<hr>
				<?php } ?>	
				<div class="bordered">	
					<div class="row-fluid inline">	
						<h5 class="text-center">PARECER</h5>
						<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
							<div class="checkbox-canvas">	
								<?php 	echo $this->BForm->input('FichaClinica.parecer', array(
									'type' => 'radio', 
									'options' => array(1 => 'Apto', 0 => 'Inapto'), 
									'hiddenField' => false, 
									'disabled' => (($verificaParecer['todos_pedidos_baixados'] == 0)? true : false), 
									'legend' => false, 
									'required' => true, 'suboption' => 1, 
									'data-option' => $subquestao['opcao_exibe_label'], 
									'after' => (($verificaParecer['todos_pedidos_baixados'] == 0)? '<span class="color-red"><strong>Há pedidos pendentes a serem baixados</strong></span>&nbsp;&nbsp;'.$this->Html->link('Baixar Pedidos de Exames', array('controller' => 'itens_pedidos_exames_baixa', 'action' => 'baixa', $dados['PedidoExame']['codigo']), array('target' => '_black', 'class' => 'btn btn-default btn-small')) : false ),
									));
									?>
								</div>
							</div>	
							<?php if(!is_null($verificaParecer['risco_por_altura'])) { ?>
							<div class="span12 no-margin-left"><hr class="margin-top-7"></div>
							<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
								<div class="checkbox-canvas">	
									<?php 	echo $this->BForm->input('FichaClinica.parecer_altura', array('type' => 'radio', 'options' => array(1 => 'Apto para trabalhar em altura', 0 => 'Inapto para trabalhar em altura'), 'hiddenField' => false, 'legend' => false, 'required' => true, 'suboption' => 1, 'data-option' => $subquestao['opcao_exibe_label']));
									?>
								</div>
							</div>
							<?php } ?>
							<?php if(!is_null($verificaParecer['risco_por_confinamento'])) { ?>
							<div class="span12 no-margin-left"><hr class="margin-top-7"></div>	
							<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
								<div class="checkbox-canvas">	
									<?php 	echo $this->BForm->input('FichaClinica.parecer_espaco_confinado', array('type' => 'radio', 'options' => array(1 => 'Apto para trabalho em espaço confinado', 0 => 'Inapto para trabalho em espaço confinado'), 'hiddenField' => false, 'legend' => false, 'required' => true, 'suboption' => 1, 'data-option' => $subquestao['opcao_exibe_label']));
									?>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>			
				</div>	

				<div class="form-actions">
					<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
					<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
				</div>


				<?php echo $this->Javascript->codeBlock("    
					$(document).ready(function(){
						setup_mascaras();
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
						$('.if-booleano').find('input[type="radio"]').click(function(event) {
							if($(this).attr('data-open')) {
								if(this.value == $(this).attr('data-open')) {
									$(this).parents('.subgroup-question').find('.inputs-config.hide').show();
								} else {
									$(this).parents('.subgroup-question').find('.inputs-config.hide').hide();
								}
								if($(this).attr('data-option')) {
									if($(this).attr('data-option') == this.value) {
										$(this).parents('.checkbox-canvas').find('.js-label.hide').show();
									} else {
										$(this).parents('.checkbox-canvas').find('.js-label.hide').hide();
									}
								}
							} else {	
								if(!isNaN(parseInt(this.value)) &&  $(this).attr('suboption') != 1) {	
									if(parseInt(this.value) > 0) {
										$(this).parents('.subgroup-question').find('.inputs-config.hide').show();
									} else {
										$(this).parents('.subgroup-question').find('.inputs-config.hide').hide();
									}
								}
								if($(this).attr('data-option')) {
									if($(this).attr('data-option') == this.value) {
										$(this).parents('.checkbox-canvas').find('.js-label.hide').show();
									} else {
										$(this).parents('.checkbox-canvas').find('.js-label.hide').hide();
									}
								}
							}
						});

						var i = 1;
						$('body').on('click', '.js-add-cid', function() {
							var html = $(this).parents('.js-encapsulado').find('.js-memory').html().replace(/xx/g, i).replace(/Xx/g, i).replace(/disabled="disabled"/g, '');
							$(this).parents('.js-encapsulado').append(html).find('.inputs-config.hide').show();
							$(this).removeClass('js-add-cid').addClass('js-remove-cid').attr('data-original-title', 'Remover doença').children('i').removeClass('icon-plus').addClass('icon-minus');
							$('[data-toggle="tooltip"]').tooltip();
							i++;
						});

						$('body').on('click', '.js-remove-cid', function() {
							$(this).parents('.inputs-config').remove();
						});


				// modulo farmaco
				var timer;
				$("body").on('keyup', '.js-farmaco', function() {
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
					$('.seleciona-farmaco').remove();
				});

				$('body').click(function(event) {
					$('.seleciona-farmaco').remove();
				});
				// ===============

				// modulo CID
				var timer;
				$("body").on('keyup', '.js-cid-10', function() {
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
					$(this).parents('.checkbox-canvas').find('.js-cid-10').val($(this).find('td:first-child').text());
					$('.seleciona-cid-10').remove();
				});

				$('body').click(function(event) {
					$('.seleciona-cid-10').remove();
				});
				// ===============

			});
		</script>
