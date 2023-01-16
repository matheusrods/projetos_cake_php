<?php echo $this->BForm->create('Cnae', array('url' => array('controller' => 'cnae','action' => 'incluir'))); ?>
<?php echo $this->element('cnae/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>