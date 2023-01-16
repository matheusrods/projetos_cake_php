<?php echo $this->BForm->create('Subperfil', array('url' => array('controller' => 'subperfil','action' => 'incluir',$codigo_cliente))); ?>
<?php echo $this->element('subperfil/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
