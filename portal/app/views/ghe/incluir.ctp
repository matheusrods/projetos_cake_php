<?php echo $this->BForm->create('Ghe', array('url' => array('controller' => 'ghe','action' => 'incluir'))); ?>
<?php echo $this->element('ghe/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>