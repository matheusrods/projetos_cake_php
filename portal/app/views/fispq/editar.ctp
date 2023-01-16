<?php echo $this->BForm->create('Fispq', array('url' => array('controller' => 'fispq', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('fispq/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>