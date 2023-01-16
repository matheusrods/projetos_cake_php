<?php echo $this->BForm->create('TipoDeficiencia', array('url' => array('controller' => 'tipos_deficiencia','action' => 'incluir'))); ?>
<?php echo $this->element('tipos_deficiencia/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>