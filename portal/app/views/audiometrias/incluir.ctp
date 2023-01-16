<?php echo $this->BForm->create('Audiometria', array('url' => array('controller' => 'audiometrias','action' => 'incluir', $codigo_item_pedido_exame))); ?>
<?php echo $this->element('audiometrias/fields', array('edit_mode' => false)); ?>
<div>
<?php echo $this->Html->link('Voltar', array('action' => 'selecionar_funcionario'), array('class' => 'btn')); ?>
	<?php echo $this->Form->button('Salvar', array('class' => 'btn btn-primary')); ?>
</div>
<?php echo $this->BForm->end(); ?>

