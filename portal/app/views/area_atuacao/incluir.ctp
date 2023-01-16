<?php echo $this->BForm->create('AreaAtuacao', array('url' => array('controller' => 'area_atuacao','action' => 'incluir', $codigo_cliente))); ?>
<?php echo $this->element('area_atuacao/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
