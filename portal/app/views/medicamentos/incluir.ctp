<?php echo $this->BForm->create('Medicamento', array('url' => array('controller' => 'medicamentos','action' => 'incluir'))); ?>
<?php echo $this->element('medicamentos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>