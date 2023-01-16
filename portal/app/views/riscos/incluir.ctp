<?php echo $this->BForm->create('Risco', array('url' => array('controller' => 'riscos','action' => 'incluir'))); ?>
<?php echo $this->element('riscos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>