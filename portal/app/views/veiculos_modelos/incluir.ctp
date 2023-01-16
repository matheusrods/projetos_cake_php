<div>
	<?php echo $this->BForm->create('VeiculoModelo');?>
	<div class='row-fluid inline'>
		<?php echo $this->Form->hidden('codigo');?>
		<?php echo $this->BForm->input('descricao',array('label' => 'Modelo','class'=>'input-large'));?>
		<?php echo $this->BForm->input('codigo_veiculo_fabricante', array('label' => 'Fabricante', 'class' => 'input-xlarge', 'options' => $fabricantes, 'empty' => 'Selecione um fabricante')); ?>
		<?php echo $this->BForm->input('codigo_veiculo_tipo', array('label' => 'Tipo', 'class' => 'input-xlarge', 'options' => $tipos, 'empty' => 'Selecione um tipo')); ?>
	</div>
	<div class='form-actions'>
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		<?= $html->link('Voltar', array('controller' => 'veiculos_modelos', 'action' => 'index'), array('class' => 'btn')); ?>
	</div>
	<?php echo $this->BForm->end(); ?>
</div>