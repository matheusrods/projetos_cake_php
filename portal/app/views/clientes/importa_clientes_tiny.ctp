

<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes'), 'type' => 'file')); ?>
<h4>Importar clientes do sistema Tiny</h4>
<?php echo $this->Form->input('documento', array('type' => 'file')); ?>
<div>&nbsp;</div>
<?php echo $this->Form->submit('Importar', array('class' => 'btn btn-primary')); ?>
<?php echo $this->Form->end(); ?>