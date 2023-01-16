<div>
	<?php echo $this->BForm->create('VeiculoTipo');?>
	<div class='row-fluid inline'>
		<?php echo $this->Form->hidden('codigo');?>
		<?php echo $this->BForm->input('descricao',array('label' => 'Tipo','class'=>'input-large'));?>
		<?php echo $this->BForm->input('codigo_veiculo_classificacao', array('label' => 'Classificação', 'class' => 'input-xlarge', 'options' => $classificacoes, 'empty' => 'Selecione a classificacao')); ?>
	</div>
	<div class='form-actions'>
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		<?= $html->link('Voltar', array('controller' => 'veiculos_tipos', 'action' => 'index'), array('class' => 'btn')); ?>
	</div>
	<?php echo $this->BForm->end(); ?>
</div>