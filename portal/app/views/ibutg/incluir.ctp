<?php echo $this->BForm->create('Ibutg', array('url' => array('controller' => 'ibutg','action' => 'incluir'))); ?>
<?php echo $this->element('ibutg/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>