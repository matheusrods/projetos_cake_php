<?php echo $this->BForm->create('UnidadesMedicao', array('url' => array('controller' => 'unidades_medicao','action' => 'incluir'))); ?>
<?php echo $this->element('unidades_medicao/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
