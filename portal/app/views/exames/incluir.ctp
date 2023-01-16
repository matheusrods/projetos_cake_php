<?php echo $this->BForm->create('Exame', array('url' => array('controller' => 'exames','action' => 'incluir'))); ?>
<?php echo $this->element('exames/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>