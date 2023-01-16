<?php echo $this->BForm->create('Medicamento', array('url' => array('controller' => 'medicamentos', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('medicamentos/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>