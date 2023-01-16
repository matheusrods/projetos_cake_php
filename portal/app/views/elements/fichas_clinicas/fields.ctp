<?php $Configuracao = &ClassRegistry::init('Configuracao'); ?>
<style type="text/css">
	.adjust-parentesco{
		border: 1px solid #a00b0b;
	}
	.bad{
		color: #D06363 !important;
	}
	.good{
		color: #8bb863 !important;
	}

</style>
<ul class="nav nav-tabs">
	<li class="active">
		<a href="#dados" data-toggle="tab">Dados Ficha Clínica</a>
	</li>
	<?php if($historico) :?>
		<li>
			<a href="#historico" data-toggle="tab">Histórico</a>
		</li>
	<?php endif; ?>
</ul>
<div class='well tab-content'>
	<div class="tab-pane active" id="dados">
	<div class="bordered">
		<div class='row-fluid'>
			<h5 class="text-center">DADOS PRINCIPAIS</h5>
			<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
			<?php echo $this->BForm->hidden('editar', array('value' => $edit_mode)); ?>
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
				<?php echo $this->BForm->input('incluido_por', array('label' => 'Incluído por:', 'style' => 'width: 95%; margin-bottom: 0', 'required' => 'required')) ?>
			</div>
			<div class="span4 checkbox-canvas padding-left-10">
				<?php echo $this->BForm->input('codigo_medico', array('label' => 'Médico:', 'options' => $dados['Medico'], 'empty' => ((!is_null($this->data))? null : 'Selecione'), 'style' => 'width: 95%; margin-bottom: 0', 'required' => 'required')) ?>
			</div>

			<?php if($this->data['hora_automatica'] == '0'): ?>

				<div class="span4 checkbox-canvas padding-left-10">
					<?php echo $this->BForm->input('hora_inicio_atendimento', array('label' => 'Horário de início de atendimento:', 'type' => 'time', 'style' => 'width: 31%; margin-bottom: 0')) ?>
				</div>
				<div class="row-fluid">
					<div class="span4 offset4 checkbox-canvas padding-left-10">
						<?php echo $this->BForm->input('hora_fim_atendimento', array('label' => 'Horário de finalização de atendimento:', 'type' => 'time', 'style' => 'width: 31%; margin-bottom: 0')) ?>
					</div>
				</div>
			<?php else: ?>
				<div class="span4 checkbox-canvas padding-left-10">
					<label>Horário de início de atendimento:</label>
					<?php echo substr($this->data['FichaClinica']['hora_inicio_atendimento'], 0, 5); ?>
				</div>
				<div class="row-fluid">
					<div class="span4 offset4 checkbox-canvas padding-left-10">
						<label>Horário de finalização de atendimento:</label>
						<?php echo substr($this->data['FichaClinica']['hora_fim_atendimento'], 0, 5); ?>
					</div>
				</div>

				<?php echo $this->BForm->hidden('hora_inicio_atendimento', array('value' => $this->data['FichaClinica']['hora_inicio_atendimento'])); ?>
				<?php echo $this->BForm->hidden('hora_fim_atendimento', array('value' => $this->data['FichaClinica']['hora_fim_atendimento'])); ?>
			<?php endif; ?>

		</div>
		<hr>
		<div class="bordered">
			<div class='row-fluid'>
				<h5 class="text-center">MEDIÇÕES</h5>
				<?php if(isset($dados_medicoes)): ?>
					<center><div id="carregado_fc"><b><u>Dados carregados automaticamente pelos questionários do Lyn</u></b></div></center>
					<br>
				<?php endif; ?>
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
						<p id="userImcMsg"  class="center label_status bad" >
							<?php echo $this->data['FichaClinica']['msg_imc']; ?>
						</p>
					</div>
				</div>


			</div>
		</div>
		<hr>

		<div class="bordered">
			<div class="row-fluid inline">

				<h5 class="text-center">PRONTUÁRIO/ANAMNESE</h5>
				<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">

					<div class="row-fluid">
						<div class="span12">
						<?php echo $this->BForm->input('FichaClinica.observacao', array(
										'type' => 'textarea', 
										'label' => false, 
										'div' => false, 
										'style' => 'width: 100%; height: 60px;'
						));?>
						
						</div>
					</div>

				</div>
			</div>
		</div>

		<hr>

		<?php //debug($questoes); ?>

		<?php foreach ($questoes as $key => $grupoQuestao) { ?>
		<div class="bordered">
			<div class='row-fluid inline'>
				<!-- TITULO DO GRUPO DE QUESTOES -->
				<?php 
					//debug($grupoQuestao['FichaClinicaGrupoQuestao']);
				?>

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

                    $data_risk = array(
                        '' => 'Selecione um risco..',
                        'Ruído' => 'Ruído',
                        'Calor' => 'Calor',
                        'Pressão' => 'Pressão',
                        'Umidade' => 'Umidade',
                        'Radiação' => 'Radiação',
                        'Vibrações' => 'Vibrações',
                        'Poeiras' => 'Poeiras',
                        'Fumos' => 'Fumos',
                        'Gases' => 'Gases',
                        'Agentes Patogênicos' => 'Agentes Patogênicos',
                        'Outros' => 'Outros'
                    );
					$data_aprazamento = array();
					$data_aprazamento[''] = 'Aprazamento..';
					for($i = 1; $i <= 24; $i++){
					    $data_aprazamento['A cada '.$i.' hora'] = 'A cada '.$i.' hora';
                    }
					$data_dose = array();
					$data_dose[''] = 'Dose..';
					$data_dose['1/2'] = '1/2';
					for($i = 1; $i <= 10; $i++){
                        $data_dose[$i] = $i;
                        $data_dose[$i.' 1/2'] = $i.' 1/2';
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
									    $keyup_menstruacao = ($label == 'ÚLTIMA MENSTRUAÇÃO:' ? 'return fnc_alerta_gravidez(this)' : '');
									    echo $this->BForm->input('FichaClinicaResposta.'.$questao['codigo'].'_resposta', array('label' => $label, 'style' => 'width: 95%; margin-bottom: 0; margin-top: -6px', 'div' => 'control-group input text width-full padding-left-10', 'required' => $required, 'onkeyup' => $keyup_menstruacao));
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
	                                    $data_hidden_mc_others = $questao['multiplas_cids_esconde_outros'];                                    
	                                    $farmaco_exibir_campo = (empty($questao['farmaco_campo_exibir']) ? '0' : $questao['farmaco_campo_exibir']);
	                                    $data_risk_active = $questao['riscos_ativo'];                                                                 	                              
										if($questao['codigo'] == 274){
											$questao['conteudo'] = '';
										}

										echo $this->BForm->input('FichaClinicaResposta.'.$questao['codigo'].'_resposta', array('type' => 'radio', 'options' => (array)json_decode($questao['conteudo']), 'default' => $default, 'data-open' => $openMenu, 'hiddenField' => false, 'legend' => false, 'required' => $required, 'data-option' => $questao['opcao_exibe_label'], 'onclick' => "return fnc_exibe_campo_farmaco(this, '".$farmaco_exibir_campo."')", 'data-hidden-mc-others' => $data_hidden_mc_others, 'data-risk-active' => $data_risk_active));


										// echo '--'. $this->data['FichaClinicaRespost;a'][$questao['codigo'].'_resposta'];

										// cria o campo livre caso exista
										if(!empty($questao['parentesco_ativo'])) {

											$hide_parentesco = 'hide';
                                        	if(isset($this->data['FichaClinicaResposta'])) {
                                            	if(!empty($this->data['FichaClinicaResposta']['parentesco'][$questao['codigo']])) {
													$hide_parentesco = '';
												}
											}

											echo $this->BForm->input('FichaClinicaResposta.parentesco.'.$questao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos'=> 'Irmãos'), 'empty' => 'Parentesco', 'class' => "adjust-parentesco {$hide_parentesco}", 'div' => false, 'label' => false));
										} 
										elseif(!empty($questao['campo_livre_label'])) {
											
											$hide_campo_livre = 'hide';
                                        	if(empty($questao['opcao_exibe_label'])) {
                                        		$hide_campo_livre = '';
											}

                                        	if(isset($this->data['FichaClinicaResposta'])) {                                            		
                                            	if(!empty($this->data['FichaClinicaResposta']['campo_livre'][$questao['codigo']])) {
													$hide_campo_livre = '';
												}
											}

											echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$questao['codigo'], array('type' => 'text', 'style' => 'width: 95%', 'label' => false, 'placeholder' => $questao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px; width: 50%;', 'div' => "js-label {$hide_campo_livre}"));
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
											$hide_parentesco = 'hide';
                                        	if(isset($this->data['FichaClinicaResposta'])) {
                                            	if(!empty($this->data['FichaClinicaResposta']['parentesco'][$questao['codigo']])) {
													$hide_parentesco = '';
												}
											}
											echo $this->BForm->input('FichaClinicaResposta.parentesco.'.$questao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'empty' => 'Parentesco', 'class' => "adjust-parentesco {$hide_parentesco}", 'div' => false, 'label' => false));
										} 
										elseif(!empty($questao['campo_livre_label'])) {
											
											// $hide_campo_livre = 'hide';
           //                              	if(empty($questao['opcao_exibe_label'])) {
           //                              		$hide_campo_livre = '';
											// }

           //                              	if(isset($this->data['FichaClinicaResposta'])) {                                            		
           //                                  	if(!empty($this->data['FichaClinicaResposta']['campo_livre'][$questao['codigo']])) {
											// 		$hide_campo_livre = '';
											// 	}
											// }

											echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$questao['codigo'], array('type' => 'text', 'style' => 'width: 95%', 'label' => false, 'placeholder' => $questao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;'));
										}
									break;
								} ?>

								<?php if($grupoQuestao['FichaClinicaGrupoQuestao']['codigo'] == 10): ?>
									<?php if(!empty($get_exames)):?>								
	                                	<!-- table exames pcmso -->
	                                	<table class="table table-striped exames_pcmso">
	                                    	<thead>
									            <tr>
									               <th class="input-medium" style="text-align:center;">EXAME</th>
									               <th class="input-medium">DATA DO RESULTADO</th>
									               <th class="input-medium" style="text-align:center;">RESULTADO</th>
									               <th class="input-medium">ANORMALIDADE</th>
									            </tr>
									        </thead>
								        	<tbody>
								        		<?php foreach ($get_exames as $key => $dados_exames): ?>
								        			<?php	        				
								        				echo $this->BForm->hidden('ItemPedidoExameBaixa.'.$key.'.codigo_exame', array('value' => $dados_exames[0]['codigo_exame'], 'id' => 'exame_cod_'.$key));   							
								        				echo $this->BForm->hidden('ItemPedidoExameBaixa.'.$key.'.codigo_itens_pedidos_exames', array('value' => $dados_exames[0]['codigo_item_pedido_exame']));

								        				echo $this->BForm->hidden('ItemPedidoExame.'.$key.'.descricao', array('value' => $dados_exames[0]['exame_descricao'], 'id' => 'exame_'.$key));    
								        			?>
								        			<tr>
										                <td class="input-mini" style="text-align:center;"><?php echo $dados_exames[0]['exame_descricao'] ?></td>
										                <td class="input-mini  resultado_data_pcmso">										       
										                	<?= $this->BForm->input('ItemPedidoExameBaixa.'.$key.'.data_realizacao_exame', array('value' => $dados_exames[0]['data_agendamento'], 'label' => false, 'style' => 'margin-right:-24px; width:80%;', 'id' => 'data_realizacao_'.$key, 'required' => true, 'data-codigo' => $key, 'type' => 'text', 'class' => 'datepickerjs date data_realizacao_resultado valor_exame_'.$dados_exames[0]['codigo_exame'], 'multiple')) ?>										         
										                </td>
										                <td class="input-mini results_exames_<?= $key ?>" style="text-align:center;">
										                	 <?php echo $this->BForm->input('ItemPedidoExameBaixa.'.$key.'.resultado', array('value' => $dados_exames[0]['resultado'], 'required' => 'required', 'id' => 'resultado_'.$key, 'legend' => false, 'options' => $dados_exames[0]['tipos_resultados'], 'type' => 'radio', 'class' => 'resultado-exame', 'multiple' => true, 'data-codigo' => $key, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
										                	
										                </td>
										                <td class="input-mini" style="text-align:center;">
										                	<?php echo $this->Form->input('ItemPedidoExameBaixa.'.$key.'.descricao', array('type' => 'textarea', 'class' => 'input-small anormalidade-exame', 'label' => false, 'id' => 'Anormalidade_'.$key, 'style' => 'height: 60px; width: 220px; font-size: 11px;', 'value' => $dados_exames[0]['anormalidade'])); ?>
										                	
										                </td>
									            	</tr>
								        		<?php endforeach ?>	
								        	</tbody>
	                                	</table>	                    
									<?php endif;?>       
	                          	<?php endif; ?>

								<!-- Monta o módulo fármaco -->
								<?php 
								if($questao['farmaco_ativo']) { 
								
									$hide_farmaco = '';
									if(!is_null($questao['farmaco_campo_exibir']) && $questao['farmaco_campo_exibir'] == 0){
										$hide_farmaco = 'hide';
									}

									if(isset($this->data['FichaClinicaResposta'])) {
                                    	if(!empty($this->data['FichaClinicaResposta']['campo_livre'][$questao['codigo']])) {
											$hide_farmaco = '';
										}
									}

								?>									
									<div class="row-fluid pull-left padding-left-10 margin-top-5">
										<div class="span3 <?php echo $hide_farmaco; ?> ">
											<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$questao['codigo'].'.farmaco', array('label' => false, 'placeholder' => 'Fármaco', 'class' => 'js-farmaco', 'div' => array('style' => 'width: 95%'))) ?>
										</div>
										<div class="span3 <?php echo $hide_farmaco; ?>" data-field-name="posologia">
											<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$questao['codigo'].'.posologia', array('label' => false, 'placeholder' => 'Posologia', 'onkeyup' => 'return fnc_exibe_aprazamento(this);', 'class' => 'js-posologia', 'div' => array('style' => 'width: 95%'))) ?>
										</div>
										<div class="span3 <?php echo $hide_farmaco; ?>" data-field-name="aprazamento">
											<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$questao['codigo'].'.aprazamento', array('type' => 'select', 'options' => $data_aprazamento, 'label' => false, 'onchange' => 'return fnc_exibe_dose(this)', 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
										</div>
	                                    <div class="span3 <?php echo $hide_farmaco; ?>" data-field-name="dose_diaria">
											<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$questao['codigo'].'.dose_diaria', array('type' => 'select', 'options' => $data_dose, 'label' => false, 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
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

						    if(!$subquestao['ativo'])
                                continue;

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


							//verifica a respostas
							if(isset($this->data['FichaClinicaResposta'])) {
								if($this->data['FichaClinicaResposta'][$questao['codigo'].'_resposta'] == '1') {
									$hide = '';
								}
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
                                        case 'COMBO':
                                        case 'SELECT':
                                            echo $this->BForm->label('FichaClinicaResposta.'.$subquestao['codigo'].'_resposta', $label, array('for' => $subquestao['codigo']));
                                            echo $this->BForm->input('FichaClinicaResposta.'.$subquestao['codigo'].'_resposta', array('type' => 'select', 'options' => (array)json_decode($subquestao['conteudo']), 'label' => '', 'default' => $default, 'data-open' => $openMenu, 'suboption' => 1, 'data-option' => $subquestao['opcao_exibe_label'], 'style' => 'width: 95%; margin-bottom: 0; margin-top: -6px; margin-left: 10px;',));
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
                                            echo $this->BForm->input('FichaClinicaResposta.'.$subquestao['codigo'].'_resposta', array('type' => 'radio', 'options' => (array)json_decode($subquestao['conteudo']), 'default' => $default, 'data-open' => $openMenu, 'hiddenField' => false, 'legend' => false, 'required' => $required, 'suboption' => 1, 'data-option' => $subquestao['opcao_exibe_label'], 'data-description-active' => $subquestao['descricao_ativo']));

											
                                            // cria o campo livre caso exista
                                            if(!empty($subquestao['parentesco_ativo'])) {

                                            	$hide_subquesta_parentesco = 'hide';
                                            	if(isset($this->data['FichaClinicaResposta'])) {
	                                            	if(!empty($this->data['FichaClinicaResposta']['parentesco'][$subquestao['codigo']])) {
														$hide_subquesta_parentesco = '';
													}
												}

                                                echo $this->BForm->input('FichaClinicaResposta.parentesco.'.$subquestao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'default' => $default, 'empty' => 'Parentesco', 'class' => "adjust-parentesco {$hide_subquesta_parentesco}", 'div' => false, 'label' => false));
                                            } 
                                            elseif(!empty($subquestao['campo_livre_label'])) {
                                            	
                                            	$hide_subquesta_campo_livre = 'hide';
                                            	if(empty($subquestao['opcao_exibe_label'])) {
                                            		$hide_subquesta_campo_livre = '';
												}

                                            	if(isset($this->data['FichaClinicaResposta'])) {                                            		
	                                            	if(!empty($this->data['FichaClinicaResposta']['campo_livre'][$subquestao['codigo']])) {
														$hide_subquesta_campo_livre = '';
													}
												}
                                            	
                                                echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$subquestao['codigo'], array('style' => 'width: 95%', 'label' => false, 'placeholder' => $subquestao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;', 'div' => "js-label {$hide_subquesta_campo_livre}"));
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

                                            	$hide_subquesta_parentesco = 'hide';
                                            	if(isset($this->data['FichaClinicaResposta'])) {                                            		
	                                            	if(!empty($this->data['FichaClinicaResposta']['parentesco'][$subquestao['codigo']])) {
														$hide_subquesta_parentesco = '';
													}
												}

                                                echo $this->BForm->input('FichaClinicaResposta.parentesco.'.$subquestao['codigo'], array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'empty' => 'Parentesco', 'class' => "adjust-parentesco {$hide_subquesta_parentesco}", 'div' => false, 'label' => false));

                                            } elseif(!empty($subquestao['campo_livre_label'])) {
                                            	
                                            	
                                                echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$subquestao['codigo'], array('type' => 'text', 'style' => 'width: 95%', 'label' => false, 'placeholder' => $subquestao['campo_livre_label'], 'style' => 'margin-bottom: 0; margin-left:10px; margin-top: -6px;'));
                                            }
										break;

									} ?>
									<!-- modulo farmaco -->
									<?php 
									if($subquestao['farmaco_ativo']) { 

										$hide_farmaco_sub = '';
										if(!is_null($subquestao['farmaco_campo_exibir']) && $subquestao['farmaco_campo_exibir'] == 0){
											$hide_farmaco_sub = 'hide';
										}

                                    	if(isset($this->data['FichaClinicaResposta'])) {                                            		
                                        	if(!empty($this->data['FichaClinicaResposta']['campo_livre'][$subquestao['codigo']])) {
												$hide_farmaco_sub = '';
											}
										}
									?>

										<div class="row-fluid pull-left padding-left-10 margin-top-5">
											<div class="span3 <?php echo $hide_farmaco_sub; ?> " data-field-name="farmaco">
												<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$subquestao['codigo'].'.farmaco', array('label' => false, 'placeholder' => 'Fármaco', 'class' => 'js-farmaco', 'div' => array('style' => 'width: 95%'))) ?>
											</div>
											<div class="span3 <?php echo $hide_farmaco_sub; ?> " data-field-name="posologia">
												<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$subquestao['codigo'].'.posologia', array('label' => false, 'placeholder' => 'Posologia', 'onkeyup' => 'return fnc_exibe_aprazamento(this);', 'class' => 'js-posologia', 'div' => array('style' => 'width: 95%'))) ?>
											</div>
	                                        <div class="span3 <?php echo $hide_farmaco_sub; ?> " data-field-name="aprazamento">
	                                            <?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$subquestao['codigo'].'.aprazamento', array('type' => 'select', 'options' => $data_aprazamento, 'label' => false, 'onchange' => 'return fnc_exibe_dose(this)', 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
	                                        </div>
											<div class="span3 <?php echo $hide_farmaco_sub; ?> " data-field-name="dose_diaria">
												<?php echo $this->BForm->input('FichaClinicaResposta.campo_livre.'.$subquestao['codigo'].'.dose_diaria', array('label' => false, 'options' => $data_dose, 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
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
								<?php if($questao['multiplas_cids_ativo']) { 

									if(!empty($this->data['FichaClinicaResposta']['campo_livre'][$questao['codigo']])) {
										$i = count(($this->data['FichaClinicaResposta']['campo_livre'][$questao['codigo']]))-1;
									
										$e = 0; 
										foreach ($this->data['FichaClinicaResposta']['campo_livre'][$questao['codigo']] as $key4 => $value) {  ?>
			                                <?php $fcr_doenca = (!empty($value['doenca']) ? $value['doenca'] : ''); ?>
			                                <?php $fcr_farmaco = (!empty($value['farmaco']) ? $value['farmaco'] : ''); ?>
			                                <?php $fcr_posologia = (!empty($value['posologia']) ? $value['posologia'] : ''); ?>
			                                <?php $fcr_aprazamento = (!empty($value['aprazamento']) ? $value['aprazamento'] : ''); ?>
			                                <?php $fcr_dose_diaria = (!empty($value['dose_diaria']) ? $value['dose_diaria'] : ''); ?>
			                                <?php $fcr_parentesco = (!empty($value['parentesco']) ? $value['parentesco'] : ''); ?>
			                                
			                                <?php 
			                                if(empty($fcr_doenca) && empty($fcr_farmaco) && empty($fcr_posologia) && empty($fcr_aprazamento) && empty($fcr_dose_diaria) && empty($fcr_parentesco)) continue; 
			                                ?>
											<div class="inputs-config span12 " style="margin-left: 0; margin-right: 1%; display: block;">
												<div class="checkbox-canvas">
													<div class="row-fluid">
														<div class="span12">

				                                            <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.'.$key4.'.doenca', array('value' => $fcr_doenca, 'class' => 'js-cid-10', 'label' => 'CID10', 'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 'div' => 'control-group input text width-full padding-left-10', 'required' => $required, 'after' => '<span style="margin-top: -7px" class="btn btn-default '.(($e == 0)? 'js-add-cid' : 'js-remove-cid').' pointer pull-right" data-toggle="tooltip" title="'.(($e == 0)? 'Adicionar doença' : 'Remover doença').'"><i class="'.(($e == 0)? 'icon-plus' : 'icon-minus').'" ></i></span>')); ?>
														</div>
													</div>
													<div class="row-fluid pull-left padding-left-10 margin-top-5">

				                                        <?php if(is_null($questao['farmaco_ativo']) || $questao['farmaco_ativo'] == 1) : ?>
															

				                                            <div class="span3 " data-field-name="farmaco">
				                                                <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.'.$key4.'.farmaco', array('value' => $fcr_farmaco, 'class' => 'js-farmaco', 'label' => false, 'placeholder' => 'Fármaco', 'div' => array('style' => 'width: 95%'))) ?>
				                                            </div>
				                                            <div class="span3" data-field-name="posologia">
				                                                <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.'.$key4.'.posologia', array('value' => $fcr_posologia, 'label' => false, 'onkeyup' => 'return fnc_exibe_aprazamento(this);', 'class' => 'js-posologia', 'placeholder' => 'Posologia', 'div' => array('style' => 'width: 95%'))) ?>
				                                            </div>
				                                            <div class="span3" data-field-name="aprazamento">
				                                                <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.'.$key4.'.aprazamento', array('type' => 'select', 'options' => $data_aprazamento, 'default' => $fcr_aprazamento, 'label' => false, 'onchange' => 'return fnc_exibe_dose(this)', 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
				                                            </div>
				                                            <div class="span3" data-field-name="dose_diaria">
				                                                <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.'.$key4.'.dose_diaria', array('type' => 'select', 'options' => $data_dose, 'default' => $fcr_dose_diaria, 'label' => false, 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
				                                            </div>
				                                        <?php endif; ?>
				                                        <?php if(!is_null($questao['multiplas_cids_exibe_parentesco']) && $questao['multiplas_cids_exibe_parentesco']) : ?>
				                                            <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.'.$key4.'.parentesco', array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'default' => $fcr_parentesco, 'empty' => 'Parentesco', 'class' => 'adjust-parentesco', 'div' => false, 'label' => false)); ?>
				                                        <?php endif; ?>
													</div>
												</div>
											</div>
										<?php 
											$e++; 
										} //fim foreach
									}//fim if
									else { 
										$e = 0; 
									} 
									?>

		                            <?php 
		                            //para imprimir na tela escondido
		                            if($e == 0) { ?>

		                            	<div class="inputs-config span12 hide" style="margin-left: 0; margin-right: 1%">
	                                        <div class="checkbox-canvas">
	                                            <div class="row-fluid">
	                                                <div class="span12">
	                                                    <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.0.doenca', array('label' => 'CID10', 'class' => 'js-cid-10', 'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 'div' => 'control-group input text width-full padding-left-10', 'required' => $required, 'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span>')); ?>
	                                                </div>
	                                            </div>
	                                            <div class="row-fluid pull-left padding-left-10 margin-top-5">
	                                                <?php if(is_null($questao['farmaco_ativo']) || $questao['farmaco_ativo'] == 1) : ?>

	                                                	<?php
														$hide_farmaco_sub_0 = '';
														if(!is_null($questao['farmaco_campo_exibir']) && $questao['farmaco_campo_exibir'] == 0){
	                                                		$hide_farmaco_sub_0 = 'hide';
														}

														if(isset($this->data['FichaClinicaResposta'])) {                                            		
				                                        	if(!empty($this->data['FichaClinicaResposta']['campo_livre'][$questao['codigo']])) {
																$hide_farmaco_sub_0 = '';
															}
														}

	                                                	?>
	                                                    <div class="span3 <?php echo $hide_farmaco_sub_0; ?>" data-field-name="farmaco">
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.0.farmaco', array('label' => false, 'placeholder' => 'Fármaco', 'class' => 'js-farmaco', 'div' => array('style' => 'width: 95%'))) ?>
	                                                    </div>
	                                                    <div class="span3 <?php echo $hide_farmaco_sub_0; ?>" data-field-name="posologia">
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.0.posologia', array('label' => false, 'onkeyup' => 'return fnc_exibe_aprazamento(this);', 'placeholder' => 'Posologia', 'class' => 'js-posologia', 'div' => array('style' => 'width: 95%'))) ?>
	                                                    </div>
	                                                    <div class="span3 <?php echo $hide_farmaco_sub_0; ?>" data-field-name="aprazamento">
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.0.aprazamento', array('type' => 'select', 'options' => $data_aprazamento, 'label' => false, 'onchange' => 'return fnc_exibe_dose(this)', 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
	                                                    </div>
	                                                    <div class="span3 <?php echo $hide_farmaco_sub_0; ?>" data-field-name="dose_diaria">
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.0.dose_diaria', array('type' => 'select', 'label' => false, 'options' => $data_dose, 'div' => array('style' => 'width: 95%'))) ?>
	                                                    </div>
	                                                <?php endif; ?>
	                                                <?php if(!is_null($questao['multiplas_cids_exibe_parentesco']) && $questao['multiplas_cids_exibe_parentesco']) : ?>
	                                                    <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.0.parentesco', array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'empty' => 'Parentesco', 'class' => 'adjust-parentesco', 'div' => false, 'label' => false)); ?>
	                                                <?php endif; ?>
	                                            </div>
	                                        </div>
	                                    </div>
									
									<?php }//fim e = 0  ?>

								<?php } ?>
								<!-- fim modulo add multiplas doencas -->

                                <?php if(!is_null($questao['riscos_ativo']) && $questao['riscos_ativo']): ?>

                                	<?php $fcrkey = 0; ?>
                                    <?php 
                                	if(isset($this->data['FichaClinicaResposta']['campo_livre'][$questao['codigo']])) {

                                    	foreach($this->data['FichaClinicaResposta']['campo_livre'][$questao['codigo']] as $riscos) : ?>
	                                        <?php if(!isset($riscos['funcao'])) continue; ?>
	                                        <div class="risco span12" style="margin-left: 0px">
	                                            <div class="checkbox-canvas span12">
	                                                <div class="row-fluid pull-left padding-left-10 margin-top-5">
	                                                    <div class="span3" data-field-name="funcao">
	                                                        <?php echo $this->BForm->label('FichaClinicaResposta.riscos.'.$questao['codigo'].'.'.$fcrkey.'.funcao_resposta', 'Função:', array('for' => $questao['codigo'])); ?>
	                                                        <?php $fcrr_funcao = (!empty($riscos['funcao']) ? $riscos['funcao'] : '');?>
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.riscos.'.$questao['codigo'].'.'.$fcrkey.'.funcao', array('type' => 'text', 'value' => $riscos['funcao'], 'label' => false, 'placeholder' => 'Função' )); ?>
	                                                    </div>
	                                                    <div class="span3" data-field-name="risco" style="margin-left: 0px;">
	                                                        <?php echo $this->BForm->label('FichaClinicaResposta.riscos.'.$questao['codigo'].'.'.$fcrkey.'.risco_resposta', 'Risco:', array('for' => $questao['codigo'])); ?>
	                                                        <?php $fcrr_risco = (!empty($riscos['risco']) ? $riscos['risco'] : ''); ?>
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.riscos.'.$questao['codigo'].'.'.$fcrkey.'.risco', array('type' => 'select', 'options' => $data_risk,  'default' => $fcrr_risco, 'label' => false, 'onchange' => 'return fnc_toggle_risco_outros(this)' )); ?>
	                                                    </div>
	                                                    <div class="span3" data-field-name="inicio" style="margin-left: 0px;">
	                                                        <?php echo $this->BForm->label('FichaClinicaResposta.riscos.'.$questao['codigo'].'.'.$fcrkey.'.inicio_resposta', 'Inicio:', array('for' => $questao['codigo'])); ?>
	                                                        <?php $fcrr_inicio = (!empty($riscos['inicio']) ? $riscos['inicio'] : ''); ?>
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.riscos.'.$questao['codigo'].'.'.$fcrkey.'.inicio', array('type' => 'text', 'value' => $fcrr_inicio, 'label' => false, 'placeholder' => 'dd/mm/aaaa' )); ?>
	                                                    </div>
	                                                    <div class="span3" data-field-name="termino" style="margin-left: 0px;">
	                                                        <?php echo $this->BForm->label('FichaClinicaResposta.riscos.'.$questao['codigo'].'.'.$fcrkey.'.termino_resposta', 'Termino:', array('for' => $questao['codigo'])); ?>
	                                                        <?php $fcrr_termino = (!empty($riscos['termino']) ? $riscos['termino'] : '');?>
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.riscos.'.$questao['codigo'].'.'.$fcrkey.'.termino', array('type' => 'text', 'value' => $fcrr_termino, 'label' => false, 'placeholder' => 'dd/mm/aaaa' )); ?>
	                                                    </div>
	                                                    <a href="#" class="btn btn-default" style="margin-top: 25px;" onclick="return fnc_toggle_risco_lista(this<?php if($fcrkey > 0) : ?>, '-'<?php endif; ?>)"><i class="icon <?php if($fcrkey <= 0) : ?>icon-plus<?php else : ?>icon-minus<?php endif; ?>"></i></a>
	                                                </div>
	                                                <div class="row-fluid pull-left padding-left-10 margin-top-5" data-field-name="risco_outros">
	                                                    <div class="span6">
	                                                        <?php $fcrr_risco_outros = (!empty($riscos['risco_outros']) ? $riscos['risco_outros'] : '')?>
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.riscos.'.$questao['codigo'].'.0.risco_outros', array('type' => 'text', 'value' => $fcrr_risco_outros, 'label' => false, 'placeholder' => 'Digite o risco não previsto:', 'style' => 'width: 99%', 'div' => array('style' => 'width: 99%') )); ?>
	                                                    </div>
	                                                </div>
	                                            </div>
	                                        </div>
	                                    	<?php $fcrkey++; ?>
                                    <?php 
                                		endforeach; 
                            		}
                                	?>


                                    <?php 
		                            //para imprimir na tela escondido
		                            if($fcrkey == 0) { ?>

	                                    <div class="riscos_list span12 hide">
	                                        <div class="risco span12" style="margin-left: 0px">
	                                            <div class="checkbox-canvas span12">
	                                                <div class="row-fluid pull-left padding-left-10 margin-top-5">
	                                                    <div class="span3" data-field-name="funcao">
	                                                        <?php echo $this->BForm->label('FichaClinicaResposta.riscos.'.$questao['codigo'].'.0.funcao_resposta', 'Função:', array('for' => $questao['codigo'])); ?>
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.riscos.'.$questao['codigo'].'.0.funcao', array('type' => 'text', 'label' => false, 'placeholder' => 'Função' )); ?>
	                                                    </div>
	                                                    <div class="span3" data-field-name="risco" style="margin-left: 0px;">
	                                                        <?php echo $this->BForm->label('FichaClinicaResposta.riscos.'.$questao['codigo'].'.0.risco_resposta', 'Risco:', array('for' => $questao['codigo'])); ?>
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.riscos.'.$questao['codigo'].'.0.risco', array('type' => 'select', 'options' => $data_risk, 'label' => false, 'onchange' => 'return fnc_toggle_risco_outros(this)' )); ?>
	                                                    </div>
	                                                    <div class="span3" data-field-name="inicio" style="margin-left: 0px;">
	                                                        <?php echo $this->BForm->label('FichaClinicaResposta.riscos.'.$questao['codigo'].'.0.inicio_resposta', 'Inicio:', array('for' => $questao['codigo'])); ?>
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.riscos.'.$questao['codigo'].'.0.inicio', array('type' => 'text', 'label' => false, 'placeholder' => 'dd/mm/aaaa' )); ?>
	                                                    </div>
	                                                    <div class="span3" data-field-name="termino" style="margin-left: 0px;">
	                                                        <?php echo $this->BForm->label('FichaClinicaResposta.riscos.'.$questao['codigo'].'.0.termino_resposta', 'Termino:', array('for' => $questao['codigo'])); ?>
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.riscos.'.$questao['codigo'].'.0.termino', array('type' => 'text', 'label' => false, 'placeholder' => 'dd/mm/aaaa' )); ?>
	                                                    </div>
	                                                    <a href="#" class="btn btn-default" style="margin-top: 25px;" onclick="return fnc_toggle_risco_lista(this)"><i class="icon icon-plus"></i></a>
	                                                </div>
	                                                <div class="row-fluid pull-left padding-left-10 margin-top-5 hide" data-field-name="risco_outros">
	                                                    <div class="span6">
	                                                        <?php echo $this->BForm->input('FichaClinicaResposta.riscos.'.$questao['codigo'].'.0.risco_outros', array('type' => 'text', 'label' => false, 'placeholder' => 'Digite o risco não previsto:', 'style' => 'width: 99%', 'div' => array('style' => 'width: 99%') )); ?>
	                                                    </div>
	                                                </div>
	                                            </div>
	                                        </div>
	                                    </div>
	                                <?php } ?>
                                <?php endif; ?>

								<div class="hide js-memory">
									<div class="inputs-config hide span12" style="margin-left: 0; margin-right: 1%">
										<div class="checkbox-canvas">
											<div class="row-fluid">
												<div class="span12">
													<?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.xx.doenca', array('disabled' => true, 'label' => 'CID10', 'class' => 'js-cid-10', 'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 'div' => 'control-group input text width-full padding-left-10', 'required' => $required, 'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span style="margin-top: -7px">')); ?>
												</div>
											</div>
											<div class="row-fluid pull-left padding-left-10 margin-top-5">
                                                <?php if(is_null($questao['farmaco_ativo']) || $questao['farmaco_ativo'] == 1) : ?>

                                                	<?php
													$hide_farmaco_sub_xx = '';
													if(!is_null($questao['farmaco_campo_exibir']) && $questao['farmaco_campo_exibir'] == 0){
                                                		$hide_farmaco_sub_xx = 'hide';
													}

													if(isset($this->data['FichaClinicaResposta'])) {                                            		
			                                        	if(!empty($this->data['FichaClinicaResposta']['campo_livre'][$questao['codigo']])) {
															$hide_farmaco_sub_xx = '';
														}
													}

                                                	?>

                                                    <div class="span3 <?php  echo $hide_farmaco_sub_xx; ?>" data-field-name="farmaco">
                                                        <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.xx.farmaco', array('disabled' => true, 'label' => false, 'class' => 'js-farmaco', 'placeholder' => 'Fármaco', 'div' => array('style' => 'width: 95%'))) ?>
                                                    </div>
                                                    <div class="span3 <?php  echo $hide_farmaco_sub_xx; ?>" data-field-name="posologia">
                                                        <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.xx.posologia', array('disabled' => true, 'label' => false, 'onkeyup' => 'return fnc_exibe_aprazamento(this);', 'class' => 'js-posologia', 'placeholder' => 'Posologia', 'div' => array('style' => 'width: 95%'))) ?>
                                                    </div>
                                                    <div class="span3 <?php  echo $hide_farmaco_sub_xx; ?>" data-field-name="aprazamento">
                                                        <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.xx.aprazamento', array('type' => 'select', 'options' => $data_aprazamento, 'label' => false, 'onchange' => 'return fnc_exibe_dose(this)', 'class' => 'js-dose_diaria', 'div' => array('style' => 'width: 95%'))) ?>
                                                    </div>
                                                    <div class="span3 <?php  echo $hide_farmaco_sub_xx; ?>" data-field-name="dose_diaria">
                                                        <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.xx.dose_diaria', array('type' => 'select', 'label' => false, 'options' => $data_dose, 'div' => array('style' => 'width: 95%'))) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if(!is_null($questao['multiplas_cids_exibe_parentesco']) && $questao['multiplas_cids_exibe_parentesco']) : ?>
                                                    <?php echo $this->BForm->input('FichaClinicaResposta.cid10.'.$questao['codigo'].'.xx.parentesco', array( 'options' => array('Pai' => 'Pai', 'Mãe' => 'Mãe', 'Irmãos' => 'Irmãos'), 'empty' => 'Parentesco', 'class' => 'adjust-parentesco', 'div' => false, 'label' => false)); ?>
                                                <?php endif; ?>
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
				
				<hr>
				<div class="bordered">
					<div class="row-fluid inline">

						<h5 class="text-center">PARECER</h5>
						<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
							<div class="checkbox-canvas">
								<?php 	echo $this->BForm->input('FichaClinica.parecer', array(
									'type' => 'radio',
									'options' => array(1 => 'Apto', 0 => 'Inapto'),
									'hiddenField' => false,
									// 'required' => 'required',					
									'disabled' => (($verificaParecer['todos_pedidos_baixados'] == 0)? true : false),
									'legend' => false,
									// 'required' => 'required', 
									'suboption' => 1,
									'data-option' => $subquestao['opcao_exibe_label'],
									// 'after' => (($verificaParecer['todos_pedidos_baixados'] == 0)? '<span class="color-red"><strong>Há pedidos pendentes a serem baixados</strong></span>&nbsp;&nbsp;'.$this->Html->link('Baixar Pedidos de Exames', array('controller' => 'itens_pedidos_exames_baixa', 'action' => 'baixa', $dados['PedidoExame']['codigo']), array('target' => '_black', 'class' => 'btn btn-default btn-small')) : false ),
									));
									?>
								</div>
							</div>

                            <?php if(strtolower($dados['PedidoExame']['tipo_pedido_exame']) != 'exame demissional') : ?>
                                <?php if(!is_null($verificaParecer['risco_por_altura'])) { ?>
                                    <?php if($verificaParecer['risco_por_altura'] == "S") { ?>
                                        <div class="span12 no-margin-left"><hr class="margin-top-7"></div>
                                        <div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
                                            <div class="checkbox-canvas">
                                                <?php 	echo $this->BForm->input('FichaClinica.parecer_altura', array(
                                                    'type' => 'radio',
                                                    'options' => array(1 => 'Apto para trabalhar em altura', 0 => 'Inapto para trabalhar em altura'),
                                                    'hiddenField' => false,
                                                    'disabled' => (($verificaParecer['todos_pedidos_baixados'] == 0)? true : false),
                                                    'legend' => false,
                                                    // 'required' => 'required',
                                                    'suboption' => 1,
                                                    'data-option' => $subquestao['opcao_exibe_label'],
                                                    // 'after' => (($verificaParecer['todos_pedidos_baixados'] == 0)? '<span class="color-red"><strong>Há pedidos pendentes a serem baixados</strong></span>&nbsp;&nbsp;' : false ),
                                                    ));
                                                ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            <?php endif; ?>

                            <?php if(strtolower($dados['PedidoExame']['tipo_pedido_exame']) != 'exame demissional') : ?>
                                <?php if(!is_null($verificaParecer['risco_por_confinamento'])) { ?>
                                    <?php if($verificaParecer['risco_por_confinamento'] == 'S') { ?>
                                        <div class="span12 no-margin-left"><hr class="margin-top-7"></div>
                                        <div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
                                            <div class="checkbox-canvas">
                                                <?php 	echo $this->BForm->input('FichaClinica.parecer_espaco_confinado', array(
                                                    'type' => 'radio',
                                                    'options' => array(1 => 'Apto para trabalho em espaço confinado', 0 => 'Inapto para trabalho em espaço confinado'),
                                                    'hiddenField' => false,
                                                    'disabled' => (($verificaParecer['todos_pedidos_baixados'] == 0)? true : false),
                                                    'legend' => false,
                                                    // 'required' => 'required',
                                                    'suboption' => 1,
                                                    'data-option' => $subquestao['opcao_exibe_label'],
                                                    // 'after' => (($verificaParecer['todos_pedidos_baixados'] == 0)? '<span class="color-red"><strong>Há pedidos pendentes a serem baixados</strong></span>&nbsp;&nbsp;' : false ),
                                                ));
                                                ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            <?php endif; ?>

					</div>
				</div>

				<div class="bordered">
					<div class="row-fluid inline">
						<div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
							<div class="checkbox-canvas span12">
	            				<?php echo $this->Form->input('FichaClinica.ficha_digitada',array('type'=>'checkbox', 'class' => 'input-xlarge ficha_digitada', 'label' => 'Ficha Digitada <abbr title="O campo deve ser preenchido apenas se a ficha clínica foi concluída. Apenas as fichas que possuírem este campo selecionado serão consideradas para emissão do relatório de Fichas Clínicas."><h11 style="font-size:0.95em;color: #00b1c4;font-weight:bold;">?</h11></abbr>')) ?>					
							</div>
	            		</div>
            		</div>
				</div>
	</div>

	<div class="form-actions">
		<div>
			<span id="div_salvar">
			<a href="javascript:void(0);" onclick="envia_ficha_clinica();" class="btn btn-primary" id="botao_submit"><i class="glyphicon glyphicon-share"></i> Salvar</a></span>
			<?php // echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
			<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
			<div id="id_img_load" style="display: none">
				<img src="/portal/img/default.gif" style="padding: 10px;">Concluindo...	            
			</div>
		</div>
	</div>
</div>

<div class="tab-pane" id="historico">&nbsp;</div>	

<?php $this->addScript($this->Buonny->link_js('moment.min')) ?>

<?php echo $this->Javascript->codeBlock("    
    $(document).ready(function(){
        setup_mascaras();
        setup_datepicker();

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

        $(document).on('change', '.resultado-exame', function(e) {
        	e.preventDefault();
        	var data = $(this).data('codigo');
        	var anormal = $('#Anormalidade_' + data);
        	
        	if($(this).val() == 1 || $(this).val() == 4 || $(this).val() == 6){
        		anormal.removeClass('show');
        		anormal.addClass('hide');
        		anormal.val('');
        	}

        	if($(this).val() == 2 || $(this).val() == 3 || $(this).val() == 5 ){        		
        		anormal.removeClass('hide');
        		anormal.addClass('show');
        	}
        });
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

        var depara = $dados_depara;
        
        if(depara.length > 1){
	        for(i = 0; i < depara.length; i++){
	            if (depara[i][0] == 15 && depara[i][1] == 1) {
	                $('#FichaClinicaResposta15Resposta1').trigger('click');
	            }
	            if ( (depara[i][0] == 10 || depara[i][0] == 11 || depara[i][0] == 12 || depara[i][0] == 13) && depara[i][1] == 1 ) {
	                $('#FichaClinicaResposta9Resposta1').trigger('click');
	                depara[i][1] = 'Sim';
	                $('input[name=\"data[FichaClinicaResposta][' + depara[i][0] + '_resposta][]\"][value=' + depara[i][1] + ']').prop('checked',true);
	            }
	            if ( depara[i][0] == 262 ){
	                if ( depara[i][1] == '0'){
	                    depara[i][1] = 'Alterado';
	                } else {
	                    depara[i][1] = 'Normal';
	                }
	            }
	            $('label[for=' + depara[i][0] + ']').append('(Informação carregada pelo Lyn)');
	            $('input[name=\"data[FichaClinicaResposta][' + depara[i][0] + '_resposta]\"][value=\"' + depara[i][1] + '\"]').prop('checked',true);
	        }
    	}
    });
    "); ?>

<script type="text/javascript">
$(document).ready(function() {
    jQuery(".if-booleano").find('input[type="checkbox"]').on('click', function(e){
        var acao = jQuery(this).val();
        switch(acao){
            case 'exibe_multiplas_cids':
            case 'subquestion_exibe_multiplas_cids':
                if(jQuery(this).is(":checked")){//esconde e mostra o farmaco
                    //jQuery(this).parent().parent().parent().parent().parent()
                    //            .find(".js-encapsulado:first").find("div.inputs-config").removeClass("hide")
                    jQuery(this).parent().parent().parent().parent().parent()
                                .find(".js-encapsulado:first > div.inputs-config.span12.hide").show();
                }else{
                    //jQuery(this).parent().parent().parent().parent().parent()
                    //            .find(".js-encapsulado:first").find("div.inputs-config").addClass("hide")
                    jQuery(this).parent().parent().parent().parent().parent()
                                .find(".js-encapsulado:first > div.inputs-config.span12.hide").hide();
                }
            break;
            case 'Sim'://caso seja qualquer checkbox de subquestao
                if(jQuery(this).is(":checked")){//esconde e mostra o farmaco
                    jQuery(this).parent().parent().parent().find("div:eq(1)").find("div:first").removeClass("hide");
                    jQuery(this).parent().parent().parent().find("div:eq(1)").find("div[data-field-name='posologia']").removeClass("hide");
                }else{
                    jQuery(this).parent().parent().parent().find("div:eq(1)").find("div:first").addClass("hide");
                    jQuery(this).parent().parent().parent().find("div:eq(1)").find("div[data-field-name='posologia']").addClass("hide");
                    jQuery(this).parent().parent().parent().find("div:eq(1)").find("div[data-field-name='aprazamento']").addClass("hide");
                    jQuery(this).parent().parent().parent().find("div:eq(1)").find("div[data-field-name='dose_diaria']").addClass("hide");
                }
            break;
            case 'subquestion_exibe_outra_alteracao':
                if(jQuery(this).is(":checked")){
                    var string_codigo = jQuery(this).attr("name").substring(0, jQuery(this).attr('name').lastIndexOf('_'));
                    var codigo = string_codigo.substring(string_codigo.lastIndexOf('[') + 1);
                    var description = '<div class="control-group input">\n' +
                        '\t <input type="text" name="data[FichaClinicaResposta][campo_livre]['+codigo+'][descricao]" placeholder="Alteração não prevista e descrição" title="Alteração não prevista e descrição" style="margin-bottom: 0; margin-left:10px; margin-top: -6px;">\n' +
                        '\t</div>';
                    if(jQuery(this).parent().parent().find(".control-group").length <= 1)
                        jQuery(this).parent().parent().append(description);
                }else{
                    jQuery(this).parent().parent().find(".control-group:eq(0)").remove();
                }
            break;
        }
    });

    $('.if-booleano').find('input[type="radio"]').click(function(event) {
        var este = $(this).parents('.if-booleano');
        if($(this).attr('data-open')) {
            if(this.value == $(this).attr('data-open') || jQuery.trim(this.value).toLowerCase() == 'alterado') {
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
        }
        else {
            if(!isNaN(parseInt(this.value)) && $(this).attr('suboption') != 1) {
                if(parseInt(this.value) > 0) {
                    este.find('.adjust-parentesco').removeClass('hide');
                    $(this).parents('.subgroup-question').find('.inputs-config.hide').show();

                    if(jQuery(this).attr('data-hidden-mc-others') == '1'){
                        jQuery(this).parent().parent().parent().parent()
                                    .find("div.js-encapsulado > div.inputs-config.span12.hide").hide();
                    }
                } else {
                    este.find('.adjust-parentesco').addClass('hide');
                    $(this).parents('.subgroup-question').find('.inputs-config.hide').hide();

                    if(jQuery(this).attr('data-hidden-mc-others') == '1'){
                        jQuery(this).parent().parent().parent().parent()
                                    .find("div.js-encapsulado > div.inputs-config.span12.hide").hide();
                    }
                }
            }
            if(!isNaN(parseInt(this.value)) && $(this).attr('suboption') == 1) {
                if(parseInt(this.value) > 0) {
                    este.find('.adjust-parentesco').removeClass('hide');
                } else {
                    este.find('.adjust-parentesco').addClass('hide');
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
        if(jQuery(this).attr('suboption')){//se for uma subquestao
            //toggle do campo de farmaco
            if(jQuery(this).val() == '1'){//sim
                jQuery(this).parent().parent().find("div:eq(1)").find("div[data-field-name='farmaco']").removeClass("hide");
            }else{//nao
                jQuery(this).parent().parent().find("div:eq(1)").find("div[data-field-name='farmaco']").addClass("hide")
            }
        }
        if(jQuery(this).attr('data-risk-active') == '1'){
            if(jQuery(this).val().toLowerCase() == 'sim' || jQuery(this).val() == '1'){
                jQuery(this).parent().parent().parent().parent().find("div.js-encapsulado:first")
                            .find("div.riscos_list:first").removeClass("hide");
            }else{
                jQuery(this).parent().parent().parent().parent().find("div.js-encapsulado:first")
                            .find("div.riscos_list:first").addClass("hide");
            }
        }
        if(jQuery(this).attr('data-description-active') == '1'){
            if(jQuery(this).val() == '1'){//sim
                var codigo = jQuery(this).attr("name").substring(jQuery(this).attr('name').lastIndexOf('[') + 1, jQuery(this).attr('name').lastIndexOf('_'));
                var description = '<div class="control-group input">\n' +
                    '\t <input type="text" name="data[FichaClinicaResposta][campo_livre]['+codigo+'][descricao]" placeholder="Descrição da Alteração" style="margin-bottom: 0; margin-left:10px; margin-top: -6px;">\n' +
                    '\t</div>';

                if(jQuery(this).parent().parent().find(".control-group").length <= 1)
                    jQuery(this).parent().parent().append(description);

            }else{
                jQuery(this).parent().parent().find(".control-group:eq(1)").remove();
            }
        }
    });

	$(".resultado-exame").each(function(indice){
		var id = $(this).prop('id');		
		var data2 = $(this).data('codigo');
	    var anormal = $('#Anormalidade_' + data2);
	    anormal.addClass('hide');

		if($('#'+id).prop('checked')) {
			var data1 = $(this).data('codigo');
	        var anormalidade = $('#Anormalidade_' + data1);

        	if($(this).val() == 2 || $(this).val() == 3 || $(this).val() == 5 ) {        		
        		anormalidade.removeClass('hide');
        		anormalidade.addClass('show');
        	} else {
        		anormalidade.addClass('hide');
        	}      
		}
	});

	$(".data_realizacao_resultado").on('change', function(){		
		var count = 0;
		$('.exames_pcmso tbody tr').each(function(){
			var data_resultado = $(this).find('.data_realizacao_resultado').val();
			if (data_resultado.length == 0 ) {
				count++;
			}
		});

		if (count == 0) {						
			jQuery("input[name='data[FichaClinica][parecer]']").attr('disabled',false);			
		} else {
			
			var checked_parecer = true;
			var checked_altura = true;
			var checked_conf = true;
			
			// Se existe o campo parecer de altura
			if(jQuery("input[name='data[FichaClinica][parecer_altura]']").length ){

				//verifica se está desabilitado
				var disabled_altura = jQuery("input[name='data[FichaClinica][parecer_altura]']").is(':disabled');

				if(!disabled_altura){
					//verifica se foi preenchido
					checked_altura = jQuery("input[name='data[FichaClinica][parecer_altura]']:checked").val();
					if(!checked_altura){						
						jQuery("input[name='data[FichaClinica][parecer_altura]']").attr('disabled',true);
						retorno = false;
					}
				}
			}

	        if(jQuery("input[name='data[FichaClinica][parecer_espaco_confinado]']").length ){

	            disabled_conf = jQuery("input[name='data[FichaClinica][parecer_espaco_confinado]']").is(':disabled');

	            //se não está desabilitado
	            if(!disabled_conf){
	                checked_conf = jQuery("input[name='data[FichaClinica][parecer_espaco_confinado]']:checked").val();
	                //se foi preenchido
	                if(!checked_conf){	                    
	                    jQuery("input[name='data[FichaClinica][parecer_espaco_confinado]']").attr('disabled',true);			
	                    retorno = false;
	                }
	            }
	        }

			checked_parecer = jQuery("input[name='data[FichaClinica][parecer]']:checked").val();
			if (!checked_parecer) {
				jQuery("input[name='data[FichaClinica][parecer]']").attr('disabled',true);				
			}
		}

		return;
	});

	$(".valor_exame_<?=$Configuracao->getChave('INSERE_EXAME_CLINICO')?>").on('change', function(){
		var valores = this.value;
		$('#FichaClinicaCodigoItemExameAso').val(valores);
	})

    /**
     * funcao para calcular o imc
     * @return {[type]} [description]
     */
    function calcula_imc()
    {
        var peso_k = $("#FichaClinicaPesoKg").val();
        var peso_g = $("#FichaClinicaPesoGr").val();

        var altura_m = $("#FichaClinicaAlturaMt").val();
        var altura_cm = $("#FichaClinicaAlturaCm").val();

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
        $("#FichaClinicaImc").val(imc.toFixed(1));
        $("#userImcMsg").text(label);
        $("#userImcMsg").removeClass();
        $("#userImcMsg").addClass(clas);

    }//fim calcula_imc

    $('.calc_imc').on('change', function(){
        calcula_imc();
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
        $(this).parents('.checkbox-canvas').find('.js-posologia').trigger('keyup');
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

    /**
     * [envia_ficha_clinica description]
     *
     * funcao para nao deixar enviar duas requisições de submit para o servidor gerando duplicidade de fichas clinicas.
     * @return {[type]} [description]
     */
    envia_ficha_clinica = function() {
        var checked_parecer = true;
        var checked_altura = true;
        var checked_conf =  true;
        var disabled_altura = false;
        var disabled_conf = false;
        var disabled_parecer = false;

        var retorno = true;

        $(".anormalidade-exame").each(function(indice){
        	var anormalidade_descricao = $(this).val();
        	anormalidade_descricao.length;
    		$(".resultado-exame").each(function(indice){
    			var id = $(this).prop('id');		
				var data2 = $(this).data('codigo');
			    var anormal = $('#Anormalidade_' + data2);	

				if($('#'+id).prop('checked')) {
					var data1 = $(this).data('codigo');
			        var anormalidade = $('#Anormalidade_' + data1);
			        var valor = $('#Anormalidade_'+ data1).val();

					if($(this).val() == 2 ) {
						if(valor == '') {
							swal({
								type: 'warning',
								title: 'Atenção',
								text: 'O campo anormalidade é obrigatorio, por favor preencha!'
							});
							$('#botao_submit').addClass('disabled')
        					retorno = false;
						}        		
					}
				}
    		});
        });

        $(".data_realizacao_resultado").each(function(indice){
        	
        	var data = $(this).data('codigo');	        
	        var data_realizacao = $('#data_realizacao_' + data);
	        var valor = $('#data_realizacao_'+ data).val();
	        
	        if(valor != ''){

		    	$('.results_exames_'+data).find('input[type="radio"]').each(function(indice) {
		    		var name_resultado = this.name;
		    		checked_resultado = jQuery("input[name='"+name_resultado+"']:checked").val();
		    		if(!checked_resultado){
		    			jQuery("input[name='"+name_resultado+"']").css('box-shadow','0 0 5px 1px red');
		    			retorno = false;
		    		}
					// Se existe o campo parecer de altura
					if(jQuery("input[name='data[FichaClinica][parecer_altura]']").length ){

						//verifica se está desabilitado
						var disabled_altura = jQuery("input[name='data[FichaClinica][parecer_altura]']").is(':disabled');

						if(!disabled_altura){
							//verifica se foi preenchido
							checked_altura = jQuery("input[name='data[FichaClinica][parecer_altura]']:checked").val();
							if(!checked_altura){
								//jQuery("input[name='data[FichaClinica][parecer_altura]']").css('outline','1px solid red');
								jQuery("input[name='data[FichaClinica][parecer_altura]']").css('box-shadow','0 0 5px 1px red');
								retorno = false;
							}
						}
					}


			        if(jQuery("input[name='data[FichaClinica][parecer_espaco_confinado]']").length ){

			            disabled_conf = jQuery("input[name='data[FichaClinica][parecer_espaco_confinado]']").is(':disabled');

			            //se não está desabilitado
			            if(!disabled_conf){
			                checked_conf = jQuery("input[name='data[FichaClinica][parecer_espaco_confinado]']:checked").val();
			                //se foi preenchido
			                if(!checked_conf){
			                    jQuery("input[name='data[FichaClinica][parecer_espaco_confinado]']").css('box-shadow','0 0 5px 1px red');
			                    retorno = false;
			                }
			            }
			        }

		    		disabled_parecer = jQuery("input[name='data[FichaClinica][parecer]']").is(':disabled');
			        //se não está desabilitado
			        if(!disabled_parecer){
			            checked_parecer = jQuery("input[name='data[FichaClinica][parecer]']:checked").val();
			             if (!checked_parecer) {
			                jQuery("input[name='data[FichaClinica][parecer]']").css('box-shadow','0 0 5px 1px red');
			                retorno = false;
			             }
			        }
		    	});
	        } else {	  
		    	$('.results_exames_'+data).find('input[type="radio"]').each(function(indice) {
		    		var name_results = this.name;
		    		var dats = $(this).data('codigo');
		    		checked_results = jQuery("input[name='"+name_results+"']:checked").val();		    		
	        		//verifica se o campo resultado esta checkado
	        		if(checked_results) {	        			
						$('#data_realizacao_'+dats).each(function(indice){
							var name_data_realizacao = this.name;
							//se o campo data da resultado nao estiver preenchido ele da false	        	
							if($("input[name='"+name_data_realizacao+"']").val().trim() == '') {	        					
								$("input[name='"+name_data_realizacao+"']").css({borderColor: 'red'});
								retorno = false;
							}
						});
	        		}
	    		});
	        }
    	});

		$(".valor_exame_<?= $Configuracao->getChave('INSERE_EXAME_CLINICO')?>").each(function(indice){
			var id = this.id;			
			var valor_data_aso = $('#'+id).val();
			var name_data_aso = this.name;
			
			if(valor_data_aso == '' || valor_data_aso == '__/__/____'){
				swal({type: 'warning',title: 'Atenção',text: 'A data resultado do ASO - EXAME CLINICO é obrigatória.'});
				jQuery("input[name='"+name_data_aso+"']").css('box-shadow','0 0 5px 1px red');
				retorno = false;
			}
		});   	

      
        if(retorno == true){

        	var codigo_do_pedido = $('#FichaClinicaCodigoPedido').val();

        	if ( $('#FichaClinicaFichaDigitada').is(":checked") ) {        		
	        	if(codigo_do_pedido){

					$.ajax({
						url: baseUrl + "fichas_clinicas/get_itens_sem_comparecimento/" + codigo_do_pedido,
						dataType: "json",
						beforeSend: function() {  
							$('#id_img_load').show();
						},
						success: function(data) {						
							if(data.return == 1) {
								alerta_exames(data);
							} else if(data.return == 2){
								alerta_exames(data);
							} else {
								submit();
							}         
						},
						complete: function(data){
							$('#id_img_load').hide();
						}
					});					
	        	}
        	} else {
        		submit();
        	}    	

        } else {
            $("#div_salvar").html("<a href=\"javascript:void(0);\" onclick=\"envia_ficha_clinica();\" class=\"btn btn-primary\" id=\"botao_submit\"><i class=\"glyphicon glyphicon-share\"></i> Salvar</a>");
        }

        return
    }//fim envia_ficha_clinica
});

function alerta_exames(data){
	swal({
		type: 'warning',
		title: 'Atenção',
		text: data.msg,
		showCancelButton: true,
		confirmButtonColor: '#228B22',
		cancelButtonColor: '#FF0000',
		cancelButtonText: 'Não',
		confirmButtonText: 'Sim',
		showLoaderOnConfirm: true
	}, 
	function(isConfirm){
		if (isConfirm) {
			submit();											
		} else {												
			return;
		}
	});
}

function submit() {
	if($('#FichaClinicaEditar').val() == 1) {
		$("#FichaClinicaEditarForm").submit();
	} else {
	   	$("#FichaClinicaIncluirForm").submit();
	}
}

function fnc_exibe_campo_farmaco(input, ecf){
    if(ecf !== '0')//caso o campo farmaco esteja exibindo por padrao..
        return;

    if(jQuery(input).val() == '1'){//sim    	
        jQuery(input).parent().parent().find("div:eq(1)").find("div:first").removeClass("hide");
        jQuery(input).parent().parent().find("div:eq(1)").find("div[data-field-name='posologia']").removeClass("hide");
    }else{
        jQuery(input).parent().parent().find("div:eq(1)").find("div:first").addClass("hide");
        jQuery(input).parent().parent().find("div:eq(1)").find("div[data-field-name='posologia']").addClass("hide");
    }
}

function fnc_alerta_gravidez(input){
    var alerta = false;

    /*if(parseInt(input.value) > 30){
        alerta = true;
    }else */if(moment(input.value, ["DD/MM/YYYY", "YYYY-MM-DD"], true).isValid()){
        if(moment().diff(moment(input.value, ["DD/MM/YYYY", "YYYY-MM-DD"], true), 'days') > 30)
            alerta = true;
    }

    if(alerta){
        swal("ATENÇÃO!", "Gravidez??", "warning");
    }

    return true;
}

function fnc_toggle_risco_lista(a_element, act){
    if(act == '-'){
        var amount_risk = jQuery(a_element).parent().parent().parent().parent().find("div.risco").length;
        if(amount_risk > 1){
            jQuery(a_element).parent().parent().parent().parent().find("div.risco:eq("+(amount_risk - 1)+")").remove();
        }
    }else{
        var new_risk = jQuery(a_element).parent().parent().parent().clone();
        var amount_risk = jQuery(a_element).parent().parent().parent().parent().find("div.risco").length;

        new_risk = new_risk[0].outerHTML;
        new_risk = new_risk.replace('[0][funcao]', '['+amount_risk+'][funcao]');
        new_risk = new_risk.replace('[0][risco]', '['+amount_risk+'][risco]');
        new_risk = new_risk.replace('[0][inicio]', '['+amount_risk+'][inicio]');
        new_risk = new_risk.replace('[0][termino]', '['+amount_risk+'][termino]');
        new_risk = new_risk.replace('[0][risco_outros]', '['+amount_risk+'][risco_outros]');
        new_risk = new_risk.replace('return fnc_toggle_risco_lista(this)', 'return fnc_toggle_risco_lista(this, \'-\')');
        new_risk = new_risk.replace('class="icon icon-plus"', 'class="icon icon-minus"');

        jQuery(a_element).parent().parent().parent().parent().append(new_risk);
    }
return false;
}

function fnc_toggle_risco_outros(select_element){
    if(jQuery.trim(select_element.value).toLowerCase() == 'outros'){
        jQuery(select_element).parent().parent().parent().parent()
                              .find("div[data-field-name='risco_outros']").removeClass("hide");
    }else{
        jQuery(select_element).parent().parent().parent().parent()
                              .find("div[data-field-name='risco_outros']").addClass("hide");
    }
return true;
}

function fnc_exibe_aprazamento(input_element){
    if(jQuery.trim(input_element.value) != ''){
        jQuery(input_element).parent().parent().parent().find("div[data-field-name='aprazamento']").removeClass("hide");
    }else{
        jQuery(input_element).parent().parent().parent().find("div[data-field-name='aprazamento']").addClass("hide");
        jQuery(input_element).parent().parent().parent().find("div[data-field-name='aprazamento']").find("select").prop('selectedIndex', 0);

        fnc_exibe_dose(jQuery(input_element).parent().parent().parent().find("div[data-field-name='dose_diaria']").find("select:first"));
    }
return true;
}

function fnc_exibe_dose(select_element){
    if(jQuery.trim(select_element.value) != '' && jQuery.trim(select_element.value) != '..'){
        jQuery(select_element).parent().parent().parent().find("div[data-field-name='dose_diaria']").removeClass("hide");
    }else{
        jQuery(select_element).parent().parent().parent().find("div[data-field-name='dose_diaria']").addClass("hide");
        jQuery(select_element).parent().parent().parent().find("div[data-field-name='dose_diaria']").find("select").prop('selectedIndex', 0);
    }
return true;
}

listar_historico();

function listar_historico() {

	var div = $('#historico');
	var codigo_pedido_exame = $("#FichaClinicaCodigoPedidoExame").val();

	$.ajax({
		type: 'post',
		url: baseUrl + 'fichas_clinicas/listagem_historico/' + codigo_pedido_exame +'/'+ Math.random(),
		cache: false,
		data: {'dados':codigo_pedido_exame },
		
		beforeSend : function(){
			bloquearDiv(div);
		},
		success: function(data){
			div.html(data);
		},
		error: function(erro,objeto,qualquercoisa){
			console.log(erro+' - '+objeto+' - '+qualquercoisa);
			div.unblock();
		}
	});
}


</script>