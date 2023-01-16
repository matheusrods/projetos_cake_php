<?php echo $this->BForm->create('PosCategorias', array('url' => array('controller' => 'pos_categorias','action' => 'incluir', $this->params['pass'][0]))); ?>
<?php echo $this->element('pos_categorias/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
