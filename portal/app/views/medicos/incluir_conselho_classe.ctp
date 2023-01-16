<?php echo $this->BForm->create('Medico', array('url' => array('controller' => 'medicos','action' => 'incluir_conselho_classe'))); ?>
<?php echo $this->element('conselho_classe/fields_conselho_classe', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>