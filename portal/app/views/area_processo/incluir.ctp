<?php echo $this->BForm->create('AreaProcesso', array('url' => array('controller' => 'area_processo','action' => 'incluir', $codigo_cliente))); ?>
<?php echo $this->element('area_processo/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
