<?php echo $this->BForm->create('MetodosTipo', array('url' => array('controller' => 'metodos_tipo','action' => 'incluir', $codigo_cliente))); ?>
<?php echo $this->element('tipos_metodos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente));?>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'metodos_tipo', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>