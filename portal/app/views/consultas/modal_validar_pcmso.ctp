<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>VALIDAR PCMSO</h3>
		</div>

		<div class="modal-body" style="max-height: 400px; font-size: 15px;">
			<center>
				<b>Unidade: </b><?php echo $dados_cliente['Unidade']['codigo'] ?><br />
				<b>Razão Social: </b><?php echo $dados_cliente['Unidade']['razao_social'] ?><br />
			</center>

			<hr style="border-bottom: 0px;">

			<center>
				<table class="table table-striped" style="margin-bottom: 0px;">
					<thead>
						<tr>
							<th class="input-mini">Setor</th>
							<th class="input-mini">Cargo</th>
							<th class="input-mini">Funcionario</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $dados_setor['Setor']['descricao'] ?></td>
							<td><?php echo $dados_cargo['Cargo']['descricao'] ?></td>
							<td><?php echo $dados_funcionario['Funcionario']['nome'] ?></td>
						</tr>
					</tbody>
				</table>
			</center>

			<?php echo $this->BForm->hidden('codigo_cliente_alocacao', array('value' => $codigo_cliente_alocacao)); ?>
			<?php echo $this->BForm->hidden('codigo_setor', array('value' => $codigo_setor)); ?>
			<?php echo $this->BForm->hidden('codigo_cargo', array('value' => $codigo_cargo)); ?>
			<?php echo $this->BForm->hidden('codigo_funcionario', array('value' => $codigo_funcionario)); ?>

			<hr style="border-bottom: 0px;">

			<center>
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
						</tr>
					</thead>
					<tbody id="lista_aplicacao_exames">
						<?php if (!empty($aplicacaoExames)) : ?>
							<?php foreach ($aplicacaoExames as $key => $aplicacaoExame) : ?>
								<?php if (is_numeric($key)) : ?>
									<tr>
										<td>
											<?php echo $this->BForm->hidden('AplicacaoExame.codigo', array('value' =>  !empty($aplicacaoExame['codigo']) ? $aplicacaoExame['codigo'] : '')); ?>
											<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.codigo_exame', array('disabled' => true, 'readonly' => true, 'placeholder' => false, 'label' => false, 'class' => 'input-xlarge', 'options' => $aplicacaoExame['Exame'])); ?>
										</td>
										<td>
											<?php echo $this->BForm->input('AplicacaoExame.periodo_meses', array('value' => $aplicacaoExame['AplicacaoExame']['periodo_meses'], 'label' => false, 'class' => 'input-mini just-number', 'multiple', 'readonly' => true)); ?><br /> Frequência (em Meses)
										</td>
										<td>
											<?php echo $this->BForm->input('AplicacaoExame.periodo_apos_demissao', array('value' => $aplicacaoExame['AplicacaoExame']['periodo_apos_demissao'], 'label' => false, 'class' => 'input-mini just-number', 'multiple', 'readonly' => true)); ?><br /> Após admissão:
										</td>
										<td>
											<?php echo $this->BForm->checkbox('AplicacaoExame.exame_admissional', array('disabled' 	=> true, 'readonly' => true, 'label' => false, 'div' => false, 'checked' => ($aplicacaoExame['AplicacaoExame']['exame_admissional'] == 1 ? 'checked' : ''))) ?> Admissional <br />
											<?php echo $this->BForm->checkbox('AplicacaoExame.exame_periodico', array('disabled' 	=> true, 'readonly' => true, 'label' => false, 'div' => false, 'checked' => ($aplicacaoExame['AplicacaoExame']['exame_periodico'] 	== 1 ? 'checked' : ''))) ?> Periódico <br />
											<?php echo $this->BForm->checkbox('AplicacaoExame.exame_demissional', array('disabled' 	=> true, 'readonly' => true, 'label' => false, 'div' => false, 'checked' => ($aplicacaoExame['AplicacaoExame']['exame_demissional'] == 1 ? 'checked' : ''))) ?> Demissional <br />
											<?php echo $this->BForm->checkbox('AplicacaoExame.exame_retorno', array('disabled' 		=> true, 'readonly' => true, 'label' => false, 'div' => false, 'checked' => ($aplicacaoExame['AplicacaoExame']['exame_retorno'] 	== 1 ? 'checked' : ''))) ?> Retorno Trabalho <br />
											<?php echo $this->BForm->checkbox('AplicacaoExame.exame_mudanca', array('disabled' 		=> true, 'readonly' => true, 'label' => false, 'div' => false, 'checked' => ($aplicacaoExame['AplicacaoExame']['exame_mudanca'] 	== 1 ? 'checked' : ''))) ?> Mudança de Riscos Ocupacionais <br />
											<?php echo $this->BForm->checkbox('AplicacaoExame.exame_monitoracao', array('disabled' 	=> true, 'readonly' => true, 'label' => false, 'div' => false, 'checked' => ($aplicacaoExame['AplicacaoExame']['exame_monitoracao'] == 1 ? 'checked' : ''))) ?> Monitoração Pontual <br />
										</td>
										<td colspan="2">
											<div class="prow">
												<div class="pspan6">
													<?php echo $this->BForm->input('AplicacaoExame.periodo_idade', array('value' => $aplicacaoExame['AplicacaoExame']['periodo_idade'], 'readonly' => true, 'disabled' => true, 'label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
												</div>
												<div class="pspan6">
													<?php echo $this->BForm->input('AplicacaoExame.qtd_periodo_idade', array('value' => $aplicacaoExame['AplicacaoExame']['qtd_periodo_idade'], 'readonly' => true, 'disabled' => true, 'label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
												</div>
											</div>

											<div class="prow">
												<div class="pspan6">
													<?php echo $this->BForm->input('AplicacaoExame.periodo_idade_2', array('value' => $aplicacaoExame['AplicacaoExame']['periodo_idade_2'], 'readonly' => true, 'disabled' => true, 'label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
												</div>
												<div class="pspan6">
													<?php echo $this->BForm->input('AplicacaoExame.qtd_periodo_idade_2', array('value' => $aplicacaoExame['AplicacaoExame']['qtd_periodo_idade_2'], 'readonly' => true, 'disabled' => true, 'label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
												</div>
											</div>

											<div class="prow">
												<div class="pspan6">
													<?php echo $this->BForm->input('AplicacaoExame.periodo_idade_3', array('value' => $aplicacaoExame['AplicacaoExame']['periodo_idade_3'], 'readonly' => true, 'disabled' => true, 'label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
												</div>
												<div class="pspan6">
													<?php echo $this->BForm->input('AplicacaoExame.qtd_periodo_idade_3', array('value' => $aplicacaoExame['AplicacaoExame']['qtd_periodo_idade_3'], 'readonly' => true, 'disabled' => true, 'label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
												</div>
											</div>

											<div class="prow">
												<div class="pspan6">
													<?php echo $this->BForm->input('AplicacaoExame.periodo_idade_4', array('value' => $aplicacaoExame['AplicacaoExame']['periodo_idade_4'], 'readonly' => true, 'disabled' => true, 'label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Idade')) ?>
												</div>
												<div class="pspan6">
													<?php echo $this->BForm->input('AplicacaoExame.qtd_periodo_idade_4', array('value' => $aplicacaoExame['AplicacaoExame']['qtd_periodo_idade_4'], 'readonly' => true, 'disabled' => true, 'label' => false, 'type' => 'text',  'class' => 'input-mini just-number', 'placeholder' => 'Meses')) ?>
												</div>
											</div>
										</td>
										<td>
											<?php $cod_selecionado = $aplicacaoExame['AplicacaoExame']['codigo_tipo_exame']; ?>
											<?php echo $this->BForm->input('AplicacaoExame.' . ($key) . '.codigo_tipo_exame', array('legend' => false, 'options' => $tipos_exames, 'type' => 'radio', 'value' => $cod_selecionado, 'disabled' => 'disabled', 'label' => array('style' => 'font-size: 10px; width: 100px; margin: 0px; line-height: 15px;', 'default' => ''),/*'value' => $aplicacao_exame['AplicacaoExame']['codigo_tipo_exame']*/)); ?>
										</td>
										<td>
											<?php echo $this->BForm->checkbox('AplicacaoExame.exame_excluido_convocacao', array('readonly' 	=> true, 'disabled' => true, 'label' => false, 'div' => false, 'checked' => $aplicacaoExame['AplicacaoExame']['exame_excluido_convocacao'] 	== 1 ? 'checked' : '')) ?> Convocação Exames<br />
											<?php echo $this->BForm->checkbox('AplicacaoExame.exame_excluido_ppp', array('readonly' 		=> true, 'disabled' => true, 'label' => false, 'div' => false, 'checked' => $aplicacaoExame['AplicacaoExame']['exame_excluido_ppp'] 		== 1 ? 'checked' : '')) ?> PPP<br />
											<?php echo $this->BForm->checkbox('AplicacaoExame.exame_excluido_aso', array('readonly' 		=> true, 'disabled' => true, 'label' => false, 'div' => false, 'checked' => $aplicacaoExame['AplicacaoExame']['exame_excluido_aso'] 		== 1 ? 'checked' : '')) ?> ASO<br />
											<?php echo $this->BForm->checkbox('AplicacaoExame.exame_excluido_pcmso', array('readonly' 		=> true, 'disabled' => true, 'label' => false, 'div' => false, 'checked' => $aplicacaoExame['AplicacaoExame']['exame_excluido_pcmso']		== 1 ? 'checked' : '')) ?> PCMSO<br />
											<?php echo $this->BForm->checkbox('AplicacaoExame.exame_excluido_anual', array('readonly'		=> true, 'disabled' => true, 'label' => false, 'div' => false, 'checked' => $aplicacaoExame['AplicacaoExame']['exame_excluido_anual'] 		== 1 ? 'checked' : '')) ?> Relatório Anual
										</td>
									</tr>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php else : ?>
							<div class="alert" style="margin-bottom: 0px;">Nenhum exame encontrado.</div>
						<?php endif; ?>
					</tbody>
				</table>
			</center>
		</div>
		<div class="modal-footer">
			<center>

				<?php
				if (empty($codigo_funcionario)) {
					$codigo_funcionario = 'null';
				}
				?>
				<?php echo $this->Html->link('Editar Pcmso', array(
					'controller' => 'aplicacao_exames', 'action' => 'editar', $codigo_cliente_alocacao, $codigo_setor, $codigo_cargo, $codigo_funcionario, 'validar_pcmso'
				), array('class' => 'btn btn-warning'));
				?>
				<?php echo $this->Html->link('Validar Pcmso', 'javascript:void(0)', array('class' => 'btn btn-success', 'title' => 'Validar Pcmso', 'onclick' => "valida_pcmso({$codigo_cliente_alocacao}, {$codigo_setor}, {$codigo_cargo}, {$codigo_funcionario})")) ?>
				<a href="javascript:void(0);" onclick="modal_validar_pcmso(0);" class="btn btn-danger">FECHAR</a>
			</center>
		</div>
	</div>
</div>

<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function() {
	   	modal_validar_pcmso = function(mostra){
	        if(mostra == 1){
	            $('#modal_validar_pcmso').css('z-index', '1050');
	            $('#modal_validar_pcmso').modal('show');
	        } else {
	            $('#modal_validar_pcmso').css('z-index', '-1');
	            $('#modal_validar_pcmso').modal('hide');
	        }
	    }	
	    modal_validar_pcmso(1);
	});
"); ?>

<script type="text/javascript">
	$(document).ready(function() {

	});

	function valida_pcmso(codigo_cliente_alocacao, codigo_setor, codigo_cargo, codigo_funcionario) {
		swal({
				type: 'warning',
				title: 'Atenção',
				text: 'Tem certeza que deseja validar este PCMSO?',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				cancelButtonText: 'Não',
				confirmButtonText: 'Sim',
				showLoaderOnConfirm: true
			},
			function() {
				//console.log('')
				$.ajax({
						url: baseUrl + 'consultas/valida_pcmso' + "/" + codigo_cliente_alocacao + "/" + codigo_setor + "/" + codigo_cargo + "/" + codigo_funcionario + "/",
						type: 'POST',
						dataType: 'json',
					})
					.done(function(response) {
						if (response == 1) {
							location.reload();
						}
					});
			});
	}
</script>