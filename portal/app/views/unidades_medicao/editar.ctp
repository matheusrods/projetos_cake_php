<?php echo $this->BForm->create('UnidadesMedicao', array('url' => array('controller' => 'unidades_medicao','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('unidades_medicao/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
