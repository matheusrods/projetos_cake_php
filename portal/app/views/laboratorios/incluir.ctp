<?php echo $this->BForm->create('Laboratorio', array('url' => array('controller' => 'laboratorios','action' => 'incluir'))); ?>
<?php echo $this->element('laboratorios/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>