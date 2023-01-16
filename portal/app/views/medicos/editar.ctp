<?php echo $this->BForm->create('Medico', array('url' => array('controller' => 'medicos', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('medicos/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>