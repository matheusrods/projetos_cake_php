<?php echo $this->BForm->create('Epi', array('url' => array('controller' => 'epi', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('epi/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>