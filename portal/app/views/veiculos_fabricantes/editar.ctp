<div>
	<?php echo $this->BForm->create('VeiculoFabricante');?>
	<div class='row-fluid inline'>
		<?php echo $this->Form->hidden('codigo');?>
		<?php echo $this->BForm->input('descricao',array('label' => 'Fabricante','class'=>'input-large'));?>
	</div>
	<div class='form-actions'>
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		<?= $html->link('Voltar', array('controller' => 'veiculos_fabricantes', 'action' => 'index'), array('class' => 'btn')); ?>
	</div>
	<?php echo $this->BForm->end(); ?>
</div>