 <div class='row-fluid'>
	<div class='row-fluid inline'>
	 	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['ClienteConfiguracao']['codigo']) ? $this->data['ClienteConfiguracao']['codigo'] : '')); ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('codigo_cliente_matricula', array('label' => 'Código Cliente Matricula (*)', 'class' => 'input-small', 'maxlength' => 8)); ?>
	</div>
</div>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'exames', 'action' => 'index'), array('class' => 'btn')); ?>
</div>

