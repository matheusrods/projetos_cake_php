<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_unidade', 'Cliente', false, 'SistCombateIncendio'); ?>
	<?php echo $this->BForm->input('codigo_setor', array('class' => 'input-xsmall', 'label' => false, 'options' => $array_setor, 'empty' => 'Todos os Setores')); ?>
	<?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => false, 'options' => array(' ' => 'Todos os Status', '0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => ' ')); ?>
	<?php echo $this->BForm->input('tipo', array('class' => 'input-xsmall', 'label' => false, 'options' => $array_tipo, 'empty' => 'Todas os Tipos')); ?>
</div>