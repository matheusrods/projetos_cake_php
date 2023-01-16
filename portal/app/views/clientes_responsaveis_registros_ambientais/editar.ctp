<?php echo $this->BForm->create('Crra', array('url' => array('controller' => 'clientes_responsaveis_registros_ambientais', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('clientes_responsaveis_registros_ambientais/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>