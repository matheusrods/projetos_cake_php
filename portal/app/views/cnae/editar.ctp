<?php echo $this->BForm->create('Cnae', array('url' => array('controller' => 'cnae', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('cnae/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>