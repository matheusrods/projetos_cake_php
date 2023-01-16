<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'FichaClinica', isset($codigo_cliente) ? $codigo_cliente : ''); ?>
	<?php echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'label' => 'Código Ficha', 'type' => 'text')) ?>
	<?php echo $this->BForm->input('codigo_funcionario', array('class' => 'input-large', 'label' => 'Funcionário', 'empty' => 'Digite o nome do funcionário')); ?>
</div>        