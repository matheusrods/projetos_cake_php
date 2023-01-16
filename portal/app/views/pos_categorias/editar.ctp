<?php echo $this->BForm->create('PosCategorias', array('url' => array('controller' => 'pos_categorias', 'action' => 'editar', $this->passedArgs[0], $codigo_cliente))); ?>
<?php echo $this->element('pos_categorias/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>
