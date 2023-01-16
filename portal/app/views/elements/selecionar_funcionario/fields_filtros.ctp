<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'Audiometria', isset($codigo_cliente) ? $codigo_cliente : ''); ?>
	<?php echo $this->BForm->hidden('tipo_busca', array('value' => 'funcionario')); ?>
	<?php echo $this->BForm->input('codigo_funcionario', array('class' => 'input-small', 'label' => 'Código Funcionário', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('nome_funcionario', array('class' => 'input-large', 'label' => 'Nome do Funcionário', 'type' => 'text')); ?>
</div>
