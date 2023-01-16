<div class="modal fade" id="modal_ppra" data-backdrop="static" style="top: 25%; left: 50%;"></div>

<div class='well'>
	<div class='row-fluid inline'>
		<h5>Empresa</h5>
		<strong>Código: </strong><?php echo $this->Html->tag('span', $dados_cliente['Matriz']['codigo']); ?>
		<strong>Razão Social: </strong><?php echo $this->Html->tag('span', $dados_cliente['Matriz']['razao_social']); ?>
	</div>
	<hr style="border:1px solid #ccc; margin:10px 0 0;" />
	<div class='row-fluid inline'>
		<h5>Unidade</h5>
		<strong>Código: </strong><?php echo $this->Html->tag('span', $dados_cliente['Unidade']['codigo']); ?>
		<strong>Razão Social: </strong><?php echo $this->Html->tag('span', $dados_cliente['Unidade']['razao_social']); ?>
	</div>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('AplicacaoExame.codigo_cliente_alocacao', array('value' => $dados_cliente['Unidade']['codigo'])); ?>

	<?php echo $this->BForm->input('AplicacaoExame.codigo_setor', array('label' => 'Setor (*)', 'class' => 'input-xlarge bselect2', 'default' => $setor_selected, 'empty' => 'Selecione', 'options' => $setores, 'onchange' => ' carregaFuncionario();', 'disabled' => ($edit_mode ? 'disabled' : ''))); ?>
	<?php if ($edit_mode && empty($ghe_selected)) : ?>
		<?php echo $this->BForm->input('AplicacaoExame.codigo_setor', array('type' => 'hidden', 'value' => $setor_selected)); ?>
	<?php endif; ?>

	<?php echo $this->BForm->input('AplicacaoExame.codigo_cargo', array('label' => 'Cargo (*)', 'class' => 'input-xlarge bselect2 cod_cargo', 'default' => $cargo_selected, 'empty' => 'Selecione', 'options' => $cargos, 'onchange' => ' carregaFuncionario();', 'disabled' => ($edit_mode ? 'disabled' : ''))); ?>
	<?php if ($edit_mode && empty($ghe_selected)) : ?>
		<?php echo $this->BForm->input('AplicacaoExame.codigo_cargo', array('type' => 'hidden', 'value' => $cargo_selected)); ?>
	<?php endif; ?>

	<a href="#" class="input-small icon-eye-open" style="margin-top: 30px;" title="Visualizar Cargo" onclick="mostra_detalhes_cargo();"></a>

	<?php echo $this->BForm->input('AplicacaoExame.codigo_funcionario', array('label' => 'Funcionário', 'class' => 'input-xlarge bselect2 cod_funcionario', 'default' => $funcionario_selected, 'empty' => 'Selecione', 'options' => $funcionarios, 'disabled' => ($edit_mode ? 'disabled' : ''))); ?>
	<?php if ($edit_mode) : ?>
		<?php echo $this->BForm->input('AplicacaoExame.codigo_funcionario', array('type' => 'hidden', 'value' => $funcionario_selected)); ?>
	<?php endif; ?>

	<?php echo $this->BForm->input('AplicacaoExame.codigo_grupo_homogeneo_exame', array('label' => 'G.H.E.', 'class' => 'input-xlarge bselect2 cod_ghe', 'default' => $ghe_selected, 'empty' => 'Selecione', 'options' => $ghes, 'disabled' => ($edit_mode ? 'disabled' : ''), 'onchange' => 'return altera_ghe_aplicacao_exame(this)')); ?>
	<?php if ($edit_mode && !empty($ghe_selected)) : ?>
		<?php echo $this->BForm->input('AplicacaoExame.codigo_grupo_homogeneo_exame', array('type' => 'hidden', 'value' => $ghe_selected)); ?>
	<?php endif; ?>

	<div id="detalhes_cargo"></div>

</div>
<div class='actionbar-right'>
	<a href="javascript:void(0)" onclick="modal_visualizar_ppra('<?php echo $this->data['Unidade']['codigo']; ?>','<?php echo key($setores); ?>','<?php echo key($cargos) ?>','<?php echo $funcionario_selected ?>', 1);">Visualizar PGR</a>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Adicionar Exame', 'onclick' => 'aplicacao.addExame();')); ?>
</div>

<table class="table table-bordered" style="font-size: 10px;" id="grupos_aplicacao_exames">
	<thead>
		<tr>
			<td><strong>Exame (*)</strong></td>
			<td colspan="2"><strong>Periodicidade:</strong></td>
			<td width="12%"><strong>Aplicável em:</strong></td>
			<td>
				<strong>A partir de qual idade?</strong><br>
				<span>(Idade)</span>
			</td>
			<td>
				<strong>Solicitar este exame <br />a cada quanto tempo?</strong><br>
				<span>(Meses)</span>
			</td>
			<td><strong>Objetivo do Exame (*)</strong></td>
			<td><strong>Tipos de Exames</strong></td>
			<td class="center"><strong>Excluir</strong></td>
		</tr>
	</thead>
	<tbody id="lista_aplicacao_exames">
		<?php if (isset($this->data['AplicacaoExame']) && !empty($this->data['AplicacaoExame'])) : ?>
			<?php foreach ($this->data['AplicacaoExame'] as $key => $aplicacao_exame) : ?>
				<?php if (is_numeric($key)) : ?>

					<?php if (!empty($aplicacao_exame['codigo_grupo_homogeneo_exame']) && !is_null($aplicacao_exame['codigo_grupo_homogeneo_exame'])) : ?>
						<?php echo $this->BForm->hidden('AplicacaoExame.' . ($key) . '.codigo_grupo_homogeneo_exame', array('value' =>  $aplicacao_exame['codigo_grupo_homogeneo_exame'])); ?>
					<?php endif; ?>

					<?php
					$style_linha = "";
					$label_inativo = "";
					$text = "";
					$label_erro_config = "";

					if (isset($aplicacao_exame['exame_ativo']) && $aplicacao_exame['exame_ativo'] == 0) {
						$style_linha = 'class="tr_inativo"';
						$label_inativo = "EXAME INATIVO";
						$text = "EXAME INATIVO!";
					} else if (isset($erro_save[$key])) {
						// $style_linha = 'class="tr_inativo"';
						$label_erro_config = "ERRO DE CONFIGURAÇÂO";
						$text = "ERRO DE CONFIGURAÇÂO!";
					}
					?>
					<tr id="linha_<?php echo $key; ?>" <?php echo $style_linha; ?> title="<?= $text; ?>">
						<td>

							<?php echo $this->BForm->hidden('AplicacaoExame.' . ($key) . '.codigo', array('value' =>  !empty($aplicacao_exame['codigo']) ? $aplicacao_exame['codigo'] : '')); ?>
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.codigo_exame', array('readonly' => (($edit_mode == 1) ? 'readonly' : ''), /*'value' => $aplicacao_exame['AplicacaoExame']['codigo_exame'],*/ 'placeholder' => false, 'label' => false, 'class' => 'input-xlarge ', 'empty' => 'Selecione', 'options' => $exames, 'onchange' => 'aplicacao.carregaExame(this, ' . $key . ');', 'default' => '')); ?>

							<?php
							if (!empty($label_inativo)) {
								echo "<br><span style='color:#fff;font-size: 20px;margin-left:10px;border-bottom: 3px solid;'>" . $label_inativo . "</span>";
							}
							if (!empty($label_erro_config)) {
								echo "<br><span style='color:red;font-size: 20px;margin-left:10px;border-bottom: 3px solid;'>" . $label_erro_config . "</span>";
							}
							?>
						</td>
						<td>
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.periodo_meses', array(/*'value' => $aplicacao_exame['AplicacaoExame']['periodo_meses'], */'label' => false, 'class' => 'input-mini just-number', 'multiple')); ?><br /> Frequência (em Meses)
						</td>
						<td>
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.periodo_apos_demissao', array(/*'value' => $aplicacao_exame['AplicacaoExame']['periodo_apos_demissao'], */'label' => false, 'class' => 'input-mini just-number', 'multiple', 'onchange' => 'aplicacao.valida_periodicidade(' . $key . ')')); ?><br /> Após admissão:
						</td>
						<td>
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.exame_admissional', array('label' => false, 'type' => 'checkbox', 'div' => false, /*'checked' => ($aplicacao_exame['AplicacaoExame']['exame_admissional'] == '1') ? 'checked' : '',*/ 'multiple' => 'checkbox')) ?> Admissional <br />
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.exame_periodico', array('label' => false, 'type' => 'checkbox', 'div' => false, /*'checked' => ($aplicacao_exame['AplicacaoExame']['exame_periodico'] == '1') ? 'checked' : '',*/ 'multiple' => 'checkbox')) ?> Periódico <br />
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.exame_demissional', array('label' => false, 'type' => 'checkbox', 'div' => false, /*'checked' => ($aplicacao_exame['AplicacaoExame']['exame_demissional'] == '1') ? 'checked' : '',*/ 'multiple' => 'checkbox')) ?> Demissional <br />
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.exame_retorno', array('label' => false, 'type' => 'checkbox', 'div' => false, /*'checked' => ($aplicacao_exame['AplicacaoExame']['exame_retorno'] == '1') ? 'checked' : '',*/ 'multiple' => 'checkbox')) ?> Retorno Trabalho <br />
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.exame_mudanca', array('label' => false, 'type' => 'checkbox', 'div' => false, /*'checked' => ($aplicacao_exame['AplicacaoExame']['exame_mudanca'] == '1') ? 'checked' : '',*/ 'multiple' => 'checkbox')) ?> Mudança de Riscos Ocupacionais <br />
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.exame_monitoracao', array('label' => false, 'type' => 'checkbox', 'div' => false, /*'checked' => ($aplicacao_exame['AplicacaoExame']['exame_mudanca'] == '1') ? 'checked' : '',*/ 'multiple' => 'checkbox')) ?> Monitoração Pontual
						</td>
						<td colspan="2">
							<div class="prow">
								<div class="pspan6">
									<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.periodo_idade', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
								</div>
								<div class="pspan6">

									<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.qtd_periodo_idade', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
								</div>
							</div>

							<div class="prow">
								<div class="pspan6">
									<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.periodo_idade_2', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
								</div>
								<div class="pspan6">

									<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.qtd_periodo_idade_2', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
								</div>
							</div>

							<div class="prow">
								<div class="pspan6">
									<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.periodo_idade_3', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
								</div>
								<div class="pspan6">

									<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.qtd_periodo_idade_3', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
								</div>
							</div>

							<div class="prow">
								<div class="pspan6">
									<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.periodo_idade_4', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
								</div>
								<div class="pspan6">

									<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.qtd_periodo_idade_4', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
								</div>
							</div>
						</td>
						<td>
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.codigo_tipo_exame', array('legend' => false, 'options' => $tipos_exames, 'type' => 'radio', 'label' => array('style' => 'font-size: 10px; width: 100px; margin: 0px; line-height: 15px;', 'default' => ''),/*'value' => $aplicacao_exame['AplicacaoExame']['codigo_tipo_exame']*/)); ?>
						</td>
						<td>
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.exame_excluido_convocacao', array('label' => false, 'type' => 'checkbox', 'div' => false, /*'checked' => ($aplicacao_exame['AplicacaoExame']['exame_excluido_convocacao'] == '1') ? 'checked' : '',*/ 'multiple' => 'checkbox')) ?> Convocação Exames<br />
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.exame_excluido_ppp', array('label' => false, 'type' => 'checkbox', 'div' => false, /*'checked' => ($aplicacao_exame['AplicacaoExame']['exame_excluido_ppp'] == '1') ? 'checked' : '',*/ 'multiple' => 'checkbox')) ?> PPP<br />
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.exame_excluido_aso', array('label' => false, 'type' => 'checkbox', 'div' => false, /*'checked' => ($aplicacao_exame['AplicacaoExame']['exame_excluido_aso'] == '1') ? 'checked' : '',*/ 'multiple' => 'checkbox')) ?> ASO<br />
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.exame_excluido_pcmso', array('label' => false, 'type' => 'checkbox', 'div' => false, /*'checked' => ($aplicacao_exame['AplicacaoExame']['exame_excluido_pcmso'] == '1') ? 'checked' : '',*/ 'multiple' => 'checkbox')) ?> PCMSO<br />
							<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.exame_excluido_anual', array('label' => false, 'type' => 'checkbox', 'div' => false, /*'checked' => ($aplicacao_exame['AplicacaoExame']['exame_excluido_anual'] == '1') ? 'checked' : '',*/ 'multiple' => 'checkbox')) ?> Relatório Anual
						</td>
						<td class="center">
							<a href="javascript:void(0);" onclick="aplicacao.removeExame(<?php echo !empty($aplicacao_exame['codigo']) ? $aplicacao_exame['codigo'] : null; ?>, <?php echo $key; ?>, this);" class="icon-trash"></a>
						</td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php else : ?>
			<tr id="linha_0">
				<td>
					<?php echo $this->BForm->input('AplicacaoExame.0.codigo_exame', array('placeholder' => false, 'label' => false, 'class' => 'input-xlarge ', 'empty' => 'Selecione', 'options' => $exames, 'onchange' => 'aplicacao.carregaExame(this, 0);', 'default' => isset($this->data['AplicacaoExame']['codigo_exame']) ? $this->data['AplicacaoExame']['codigo_exame'] : '')); ?>
				</td>
				<td>
					<?php echo $this->BForm->input('AplicacaoExame.0.periodo_meses', array('label' => false, 'class' => 'input-mini just-number', 'multiple')); ?><br /> Frequência (em Meses)
				</td>
				<td>
					<?php echo $this->BForm->input('AplicacaoExame.0.periodo_apos_demissao', array('label' => false, 'class' => 'input-mini just-number', 'multiple', 'onchange' => 'aplicacao.valida_periodicidade(0)')); ?><br /> Após admissão:
				</td>
				<td>
					<?php echo $this->BForm->input('AplicacaoExame.0.exame_admissional', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Admissional <br />
					<?php echo $this->BForm->input('AplicacaoExame.0.exame_periodico', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Periódico <br />
					<?php echo $this->BForm->input('AplicacaoExame.0.exame_demissional', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Demissional <br />
					<?php echo $this->BForm->input('AplicacaoExame.0.exame_retorno', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Retorno Trabalho <br />
					<?php echo $this->BForm->input('AplicacaoExame.0.exame_mudanca', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Mudança de Riscos Ocupacionais
				</td>
				<td colspan="2">
					<div class="prow">
						<div class="pspan6">
							<?php echo $this->BForm->input('AplicacaoExame.0.periodo_idade', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
						</div>
						<div class="pspan6">

							<?php echo $this->BForm->input('AplicacaoExame.0.qtd_periodo_idade', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
						</div>
					</div>

					<div class="prow">
						<div class="pspan6">
							<?php echo $this->BForm->input('AplicacaoExame.0.periodo_idade_2', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
						</div>
						<div class="pspan6">

							<?php echo $this->BForm->input('AplicacaoExame.0.qtd_periodo_idade_2', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
						</div>
					</div>

					<div class="prow">
						<div class="pspan6">
							<?php echo $this->BForm->input('AplicacaoExame.0.periodo_idade_3', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
						</div>
						<div class="pspan6">

							<?php echo $this->BForm->input('AplicacaoExame.0.qtd_periodo_idade_3', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
						</div>
					</div>

					<div class="prow">
						<div class="pspan6">
							<?php echo $this->BForm->input('AplicacaoExame.0.periodo_idade_4', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
						</div>
						<div class="pspan6">

							<?php echo $this->BForm->input('AplicacaoExame.0.qtd_periodo_idade_4', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
						</div>
					</div>
				</td>
				<td>
					<?php echo $this->BForm->input('AplicacaoExame.0.codigo_tipo_exame', array('legend' => false, 'options' => $tipos_exames, 'type' => 'radio', 'label' => array('style' => 'font-size: 10px; width: 100px; margin: 0px; line-height: 15px;', 'default' => ''))); ?>
				</td>
				<td>
					<?php echo $this->BForm->input('AplicacaoExame.0.exame_excluido_convocacao', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Convocação Exames<br />
					<?php echo $this->BForm->input('AplicacaoExame.0.exame_excluido_ppp', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> PPP<br />
					<?php echo $this->BForm->input('AplicacaoExame.0.exame_excluido_aso', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> ASO<br />
					<?php echo $this->BForm->input('AplicacaoExame.0.exame_excluido_pcmso', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> PCMSO<br />
					<?php echo $this->BForm->input('AplicacaoExame.0.exame_excluido_anual', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Relatório Anual
				</td>
				<td>
					<a href="javascript:void(0);" onclick="aplicacao.removeExame(null, 0, this);"><label class="icon-trash"></label></a>
				</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
<div id="modelos">
	<table id="modelo_aplicacao_exames" style="display: none;">
		<tr>
			<td>
				<?php echo $this->BForm->hidden('AplicacaoExame.X.codigo', array('value' =>  '')); ?>
				<?php echo $this->BForm->input('AplicacaoExame.X.codigo_exame', array('placeholder' => false, 'label' => false, 'class' => 'X', 'empty' => 'Selecione', 'options' => $exames, 'default' => isset($this->data['AplicacaoExame']['codigo_exame']) ? $this->data['AplicacaoExame']['codigo_exame'] : '')); ?>
			</td>
			<td>
				<?php echo $this->BForm->input('AplicacaoExame.X.periodo_meses', array('label' => false, 'class' => 'input-mini just-number', 'multiple')); ?><br /> Frequência (em Meses)
			</td>
			<td>
				<?php echo $this->BForm->input('AplicacaoExame.X.periodo_apos_demissao', array('label' => false, 'class' => 'input-mini just-number', 'multiple')); ?><br /> Após admissão:
			</td>
			<td>
				<?php echo $this->BForm->input('AplicacaoExame.X.exame_admissional', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Admissional <br />
				<?php echo $this->BForm->input('AplicacaoExame.X.exame_periodico', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Periódico <br />
				<?php echo $this->BForm->input('AplicacaoExame.X.exame_demissional', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Demissional <br />
				<?php echo $this->BForm->input('AplicacaoExame.X.exame_retorno', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Retorno Trabalho <br />
				<?php echo $this->BForm->input('AplicacaoExame.X.exame_mudanca', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Mudança de Riscos Ocupacionais<br />
				<?php echo $this->BForm->input('AplicacaoExame.X.exame_monitoracao', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Monitoração Pontual
			</td>


			<td colspan="2">
				<div class="prow">
					<div class="pspan6">
						<?php echo $this->BForm->input('AplicacaoExame.X.periodo_idade', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
					</div>
					<div class="pspan6">

						<?php echo $this->BForm->input('AplicacaoExame.X.qtd_periodo_idade', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
					</div>
				</div>

				<div class="prow">
					<div class="pspan6">
						<?php echo $this->BForm->input('AplicacaoExame.X.periodo_idade_2', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
					</div>
					<div class="pspan6">

						<?php echo $this->BForm->input('AplicacaoExame.X.qtd_periodo_idade_2', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
					</div>
				</div>

				<div class="prow">
					<div class="pspan6">
						<?php echo $this->BForm->input('AplicacaoExame.X.periodo_idade_3', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
					</div>
					<div class="pspan6">

						<?php echo $this->BForm->input('AplicacaoExame.X.qtd_periodo_idade_3', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
					</div>
				</div>

				<div class="prow">
					<div class="pspan6">
						<?php echo $this->BForm->input('AplicacaoExame.X.periodo_idade_4', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
					</div>
					<div class="pspan6">

						<?php echo $this->BForm->input('AplicacaoExame.X.qtd_periodo_idade_4', array('label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
					</div>
				</div>
			</td>
			<td>
				<?php foreach ($tipos_exames as $key_tipo => $item) : ?>
					<input type="radio" name="data[AplicacaoExame][X][codigo_tipo_exame]" value="<?php echo $key_tipo; ?>" /> <?php echo $item; ?><br />
				<?php endforeach; ?>
			</td>
			<td>
				<?php echo $this->BForm->input('AplicacaoExame.X.exame_excluido_convocacao', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Convocação Exames<br />
				<?php echo $this->BForm->input('AplicacaoExame.X.exame_excluido_ppp', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> PPP<br />
				<?php echo $this->BForm->input('AplicacaoExame.X.exame_excluido_aso', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> ASO<br />
				<?php echo $this->BForm->input('AplicacaoExame.X.exame_excluido_pcmso', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> PCMSO<br />
				<?php echo $this->BForm->input('AplicacaoExame.X.exame_excluido_anual', array('label' => false, 'type' => 'checkbox', 'div' => false, 'multiple' => 'checkbox')) ?> Relatório Anual
			</td>
			<td>
				<a href="javascript:void(0);" class="icon-trash"></a>
			</td>
		</tr>
	</table>
</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link(
		'Voltar',
		(Comum::UrlOrigem() ? Comum::UrlOrigem()->data : array('controller' => 'aplicacao_exames', 'action' => 'index', (isset($this->data['Unidade']['codigo']) && !empty($this->data['Unidade']['codigo']) ? $this->data['Unidade']['codigo'] : $dados_cliente['Unidade']['codigo']))),
		array('class' => 'btn')
	);  ?>
</div>

<div class="modal fade" id="modal_carregando">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Aguarde, carregando informações...</h4>
			</div>
			<div class="modal-body">
				<img src="/portal/img/ajax-loader.gif" style="padding: 10px;">
			</div>
		</div>
	</div>
</div>

<?php echo $this->Buonny->link_js('aplicacao_exames'); ?>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();

		$(".modal").css("z-index", "-1");
		$(".modal").css("width", "35%");
	});

	function carregaFuncionario(){
		var codigo_setor = $("#AplicacaoExameCodigoSetor").val();
		var codigo_cargo = $("#AplicacaoExameCodigoCargo").val();
		var codigo_cliente = $("#AplicacaoExameCodigoClienteAlocacao").val();
		$("#GrupoExposicaoCodigoFuncionario").html("<option value=\'\'>Selecione</option>");



		if(codigo_setor != "" && codigo_cargo != ""){
			$.ajax({
				type: "POST",
				url: baseUrl + "funcionarios/carrega_funcionario/" + Math.random(),
				dataType: "json",
				data:{"codigo_cliente": codigo_cliente, "codigo_setor": codigo_setor, "codigo_cargo": codigo_cargo, },
				beforeSend: function(){
						$("#AplicacaoExameCodigoFuncionario").children().remove();
						$("#AplicacaoExameCodigoFuncionario").append("<option value=\'\'>Carregando...</option>");

				},
				success: function(data){
					$("#AplicacaoExameCodigoFuncionario").children().remove();
						$("#AplicacaoExameCodigoFuncionario").append("<option value=\'\'>Selecione</option>");

					if(data != ""){
						$.each(data, function(id, dados) {
							$("#AplicacaoExameCodigoFuncionario").append("<option value=\'" + id + "\'>" + dados + "</option>");
						});
					}
				}

			});
		}
	}

	function modal_visualizar_ppra(codigo_unidade,setor,cargo,funcionario,mostra) {
		if(mostra) {

			var div = jQuery("div#modal_ppra");
			bloquearDiv(div);
			div.load(baseUrl + "aplicacao_exames/modal_ppra_pendente/" + codigo_unidade + "/" + setor + "/" + cargo + "/" + funcionario + "/" + Math.random());

			$("#modal_ppra").css("z-index", "1050");
			$("#modal_ppra").css("width", "40%");
			$("#modal_ppra").modal("show");

		} else {
			$(".modal").css("z-index", "-1");
			$("#modal_ppra").modal("hide");
		}
	}

');
?>

<style type="text/css">
	.prow {
		padding: 4px 0px 0 0px;
		/* float: left; */
		width: 100%;
		/* position: relative; */
		float: left;
		border: 1px solid #ccc;
		clear: both;
		margin-bottom: 10px;
	}

	.pspan6 {
		float: left;
		width: 49%;
		display: inline-block;
		text-align: center;
		margin-left: 0px;
	}

	td .prow:last-child {
		margin-bottom: 0;
	}

	.pspan6 .control-group {
		margin-bottom: 4px;
	}

	.tr_inativo {
		background-color: #ff8888;
	}
</style>
<?php echo $javascript->link('comum.js'); ?>

<?php if (!empty($visualizar_gae) && $visualizar_gae) : ?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery("input, select, textarea").attr("disabled", "disabled");
			jQuery(".icon-plus").parent().remove();
			jQuery(".icon-trash").remove();
			jQuery("input[type='submit']").remove();
		});
	</script>
<?php endif; ?>