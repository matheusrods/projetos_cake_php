<?php echo $this->BForm->create('Medico', array('url' => array('controller' => 'medicos','action' => 'incluir'))); ?>
<?php echo $this->element('medicos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>