
<?php echo $this->BForm->create('TVeicVeiculo', array('url' => array('controller' => 'Veiculos','action' => 'consultar_checklist')));?>
<div id="form-pai" class='row-fluid inline'>
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'TVeicVeiculo') ?>
	<?php echo $this->BForm->input('veic_placa', array('label' => 'Placa','type' => 'text','class' => 'input-small placa-veiculo')) ?>
	<?php echo $this->Buonny->input_validade_checklist($this, $regras_aceite_sm, 'TVeicVeiculo','racs_validade_checklist','Regra Aceite SM','Selecione',true) ?>
	<?php echo $this->BForm->input('checklist_dias_validos', array('label' => 'Qtd. Dias Regra','type' => 'text','class' => 'input-small numeric just-number', 'placeholder' => 'dias', 'readonly'=>true)) ?>
</div>
<div class="form-actions">
	<?php echo $this->BForm->submit('Localizar', array('div' => false, 'class' => 'btn btn-primary')); ?>
</div>
<?php echo $this->Javascript->codeBlock('	
	$("#TVeicVeiculoCodigoCliente").blur();
	$(function(){
		setup_mascaras();
		$("#TVeicVeiculoRacsValidadeChecklist").change();
	});', false);
?>