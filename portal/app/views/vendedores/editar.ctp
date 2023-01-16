<?php echo $this->BForm->create('Vendedor', array('url' => array('controller' => 'vendedores', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('vendedores/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>