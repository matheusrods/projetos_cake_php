<?php echo $this->BForm->create('Vendedor', array('url' => array('controller' => 'vendedores','action' => 'incluir'))); ?>
<?php echo $this->element('vendedores/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>