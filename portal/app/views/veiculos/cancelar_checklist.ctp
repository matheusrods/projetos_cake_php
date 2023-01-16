<div class="lista">
	<?php echo $this->BForm->create('TCveiChecklistVeiculo', array('url' => array('controller' => 'Veiculos','action' => 'cancelar_checklist'),'type'=>'POST'));?>

	<div class="well">
		<?php echo $this->BForm->hidden("veic_placa",array('value' =>$veic_placa))?>
		<?php echo $this->BForm->hidden("TCveiChecklistVeiculo.cvei_codigo",array('value' =>$this->data['TCveiChecklistVeiculo']['cvei_codigo']))?>
		<?php echo $this->BForm->hidden("TCveiChecklistVeiculo.cvei_pess_oras_codigo",array('value' =>$this->data['TCveiChecklistVeiculo']['cvei_pess_oras_codigo']))?>
		<?php echo $this->BForm->hidden("TCveiChecklistVeiculo.cvei_veic_oras_codigo",array('value' =>$this->data['TCveiChecklistVeiculo']['cvei_veic_oras_codigo']))?>
		<div class="row-fluid inline">
			<strong>Data do Cancelamento:</strong> <?php echo $this->BForm->hidden("TCveiChecklistVeiculo.cvei_data_cancelamento", array('value' =>$this->data['TCveiChecklistVeiculo']['cvei_data_cancelamento'] )) ?><?php echo $this->data['TCveiChecklistVeiculo']['cvei_data_cancelamento']; ?>
		</div>
		<div class="row-fluid inline">
			<strong>Usuário Cancelamento:</strong> <?php echo $this->BForm->hidden("TCveiChecklistVeiculo.cvei_usuario_cancelamento", array('value' =>$this->data['TCveiChecklistVeiculo']['cvei_usuario_cancelamento'])) ?><?php echo $this->data['TCveiChecklistVeiculo']['cvei_usuario_cancelamento']; ?>
		</div>
	</div>
	<div class="row-fluid inline">
	    <?php echo $this->BForm->input('cvei_mcch_codigo', array('label' => false, 'class' => 'input-xxlarge', 'options' => $motivo, 'empty' => 'Motivo'));?>
	</div>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success', 'id'=>'botao-submit')); ?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function(){
		$('#botao-submit').click(function(){
			jQuery(document).ready(function(){
				bloquearDiv($('div.lista'));
			});
		});
	});
");?>

