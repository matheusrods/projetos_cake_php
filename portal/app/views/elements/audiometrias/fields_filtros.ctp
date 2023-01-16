<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'Audiometria', isset($codigo_cliente) ? $codigo_cliente : ''); ?>

	<?php // echo $this->BForm->input('codigo', array('class' => 'input-small just-number', 'label' => 'Código relatório', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('nome_funcionario', array('class' => 'input-large', 'label' => 'Funcionário', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('cpf', array('class' => 'input-large only-number', 'maxlength' => 11, 'label' => 'CPF', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('data_exame', array('class' => 'input-small data', 'label' => 'Data do exame', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('data_solicitacao', array('class' => 'input-small data', 'label' => 'Data do pedido', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('tipo_exame', array('options' => $tipos_exames, 'empty' => 'Todos', 'class' => 'input-medium', 'label' => 'Tipo do exame')); ?>
</div>

 <?php 
echo $this->Javascript->codeBlock("
    setup_datepicker();
");
?>