<?php echo $this->BForm->create('AreaProcesso', array('url' => array('controller' => 'area_processo','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('area_processo/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
