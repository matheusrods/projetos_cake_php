<?php echo $this->BForm->create('TipoGlosas', array('url' => array('controller' => 'tipo_glosas', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('tipo_glosas/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>