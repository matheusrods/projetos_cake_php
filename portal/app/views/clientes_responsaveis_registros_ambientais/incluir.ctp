<?php echo $this->BForm->create('Crra', array('url' => array('controller' => 'clientes_responsaveis_registros_ambientais','action' => 'incluir'))); ?>
<?php echo $this->element('clientes_responsaveis_registros_ambientais/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>