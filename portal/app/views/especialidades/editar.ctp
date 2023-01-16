<?php echo $this->BForm->create('Especialidade', array('url' => array('controller' => 'especialidades', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('especialidades/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>