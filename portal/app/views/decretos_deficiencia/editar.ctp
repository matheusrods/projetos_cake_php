<?php echo $this->BForm->create('DecretoDeficiencia', array('url' => array('controller' => 'decretos_deficiencia', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('decretos_deficiencia/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>