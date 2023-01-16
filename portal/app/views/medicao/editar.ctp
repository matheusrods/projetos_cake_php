<?php echo $this->BForm->create('Medicao', array('url' => array('controller' => 'medicao', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('medicao/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>