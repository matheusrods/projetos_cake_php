<?php echo $this->BForm->create('DecretoDeficiencia', array('url' => array('controller' => 'decretos_deficiencia','action' => 'incluir'))); ?>
<?php echo $this->element('decretos_deficiencia/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>