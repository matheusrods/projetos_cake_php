<?php echo $this->BForm->create('SistCombateIncendio', array('url' => array('controller' => 'sist_combate_incendio', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('sist_combate_incendio/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>