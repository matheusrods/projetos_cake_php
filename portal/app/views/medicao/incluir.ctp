<?php echo $this->BForm->create('Medicao', array('url' => array('controller' => 'medicao','action' => 'incluir'))); ?>
<?php echo $this->element('medicao/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>