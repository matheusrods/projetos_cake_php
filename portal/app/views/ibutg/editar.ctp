<?php echo $this->BForm->create('Ibutg', array('url' => array('controller' => 'ibutg', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('ibutg/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>