<?php echo $this->BForm->create('Fispq', array('url' => array('controller' => 'fispq','action' => 'incluir'))); ?>
<?php echo $this->element('fispq/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>