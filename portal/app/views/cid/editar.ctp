<?php echo $this->BForm->create('Cid', array('url' => array('controller' => 'cid', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('cid/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>