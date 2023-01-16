<?php echo $this->BForm->create('TipoGlosas', array('url' => array('controller' => 'tipo_glosas','action' => 'incluir'))); ?>
<?php echo $this->element('tipo_glosas/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>