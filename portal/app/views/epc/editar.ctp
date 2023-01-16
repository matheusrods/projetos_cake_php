<?php echo $this->BForm->create('Epc', array('url' => array('controller' => 'epc', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('epc/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>