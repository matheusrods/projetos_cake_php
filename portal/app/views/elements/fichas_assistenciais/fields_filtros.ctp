<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'FichaAssistencial', isset($codigo_cliente) ? $codigo_cliente : ''); ?>

	<?php echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'label' => 'Código Ficha', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('codigo_pedido_exame', array('class' => 'input-small just-number', 'label' => 'Código Pedido', 'type'=> 'text')); ?>
	<?php echo $this->BForm->input('nome_funcionario', array('class' => 'input-large', 'label' => 'Funcionário', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('nome_medico', array('class' => 'input-large', 'label' => 'Médico', 'type' => 'text')); ?>
</div>
<?php 
	echo $this->Javascript->codeBlock("	
		setup_mascaras();
	");
?>