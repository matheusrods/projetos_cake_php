<?php echo $this->BForm->create('Produto', array('url' => array('controller' => 'produtos', 'action' => 'editar',$this->passedArgs[0]))); ?>
<?php echo $this->element('produtos/fields'); ?>