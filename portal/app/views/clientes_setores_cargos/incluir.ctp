<?php echo $this->BForm->create('Epc', array('url' => array('controller' => 'epc','action' => 'incluir'))); ?>
<?php echo $this->element('epc/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>