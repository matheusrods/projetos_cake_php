<?php echo $this->BForm->create('Especialidade', array('url' => array('controller' => 'especialidades','action' => 'incluir'))); ?>
<?php echo $this->element('especialidades/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>