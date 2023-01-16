<?php echo $this->BForm->create('Laboratorio', array('url' => array('controller' => 'laboratorios', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('laboratorios/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>