<?php echo $this->BForm->create('TipoSistIncendio', array('url' => array('controller' => 'tipos_sist_incendio','action' => 'incluir'))); ?>
<?php echo $this->element('tipos_sist_incendio/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>