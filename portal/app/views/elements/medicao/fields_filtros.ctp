<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente($this, 'unidade', 'Cliente', false,'Medicao'); ?>
	<?php echo $this->BForm->input('codigo_setor', array('class' => 'input-xsmall', 'label' => false, 'options' => $array_setor, 'empty' => 'Todos os Setores')); ?>
	<?php echo $this->BForm->input('codigo_cargo', array('class' => 'input-xsmall', 'label' => false, 'options' => $array_cargo, 'empty' => 'Todos os Cargos')); ?>
	<?php echo $this->BForm->input('codigo_risco', array('class' => 'input-xsmall', 'label' => false, 'options' => $array_risco, 'empty' => 'Todos os Riscos')); ?>  
</div>        