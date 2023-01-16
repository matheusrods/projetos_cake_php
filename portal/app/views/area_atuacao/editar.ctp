<?php echo $this->BForm->create('AreaAtuacao', array('url' => array('controller' => 'area_atuacao','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('area_atuacao/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
