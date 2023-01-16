<?php echo $this->BForm->create('TViagViagem',array('url' => array('controller' => 'viagens','action' => 'justificar_inicio_sem_checklist', $veic_placa,$cliente), 'type' => 'POST')); ?>
	<div class="well">
		<strong>Placa: </strong><?php echo $veic_placa; ?> <strong>Cliente: </strong><?php echo $cliente; ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('viag_justificativa',array('type' => 'textarea','class' => 'input-xxlarge', 'label' => 'Justificativa')) ?>
	</div>
	<div class="row-fluid inline" id="sms">
		<h4>SMs</h4>
		<?php echo $this->BForm->input('viag_codigo_sm', array('multiple' => 'checkbox', 'options' => $viagens, 'label' => '', 'class' => 'checkbox inline input-large')); ?>	
    </div>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Autorizar', array('div' => false, 'class' => 'btn btn-success')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
