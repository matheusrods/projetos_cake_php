<?php echo $this->BForm->create('SistCombateIncendio', array('url' => array('controller' => 'sist_combate_incendio','action' => 'incluir'))); ?>
<?php echo $this->element('sist_combate_incendio/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>