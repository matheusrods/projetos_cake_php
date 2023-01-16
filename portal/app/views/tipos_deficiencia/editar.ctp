<?php echo $this->BForm->create('TipoDeficiencia', array('url' => array('controller' => 'tipos_deficiencia', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('tipos_deficiencia/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>