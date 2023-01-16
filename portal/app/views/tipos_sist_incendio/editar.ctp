<?php echo $this->BForm->create('TipoSistIncendio', array('url' => array('controller' => 'tipos_sist_incendio', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('tipos_sist_incendio/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>