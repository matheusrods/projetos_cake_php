<?php echo $this->BForm->create('TipoAfastamento', array('url' => array('controller' => 'tipos_afastamento','action' => 'incluir'))); ?>
<?php echo $this->element('tipos_afastamento/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>