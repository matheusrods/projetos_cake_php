<div class="well">
	<div class='row-fluid inline'>
		<?php echo $this->BForm->hidden('vcen_refe_referencia') ?>
		<?php echo $this->BForm->input('vcen_refe_referencia_visual',array('readonly'=>true,'label'=>'CD','value'=>$dados_checklist_entrada['TRefeReferencia']['refe_descricao'])) ?>
		<?php echo $this->BForm->input('TVeicVeiculo.veic_placa', array('class' => 'input-small placa-veiculo', 'label'=>'Placa Veiculo', 'readonly'=>true)) ?>
		<?php echo $this->BForm->input('TVeicVeiculoCarreta.veic_placa', array('class' => 'input-small placa-veiculo',  'label'=>'Placa Carreta','readonly'=>true)) ?>
		<?php echo $this->BForm->input('TPessPessoa.pess_nome', array('class' => 'input-xlarge', 'label' => 'Nome Motorista', 'readonly' => true)) ?>
		<label>Aprovado?</label>
		<?php echo $this->BForm->input("TVcenViagemChecklistEntrada.vcen_aprovado", array('type' => 'radio','class'=>'radio_aprovado','options' => $array_sim_nao,'disabled'=>true,'div'=>false,'legend' => false, 'label' => array('class' => 'radio inline', 'value'=>'Aprovado'))) ?>
	</div>
</div>
