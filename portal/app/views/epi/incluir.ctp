<?php echo $this->BForm->create('Epi', array('url' => array('controller' => 'epi','action' => 'incluir'))); ?>
<?php echo $this->element('epi/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>