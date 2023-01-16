<?php echo $this->BForm->create('TipoAfastamento', array('url' => array('controller' => 'tipos_afastamento', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('tipos_afastamento/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>