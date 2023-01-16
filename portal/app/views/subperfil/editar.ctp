<?php echo $this->BForm->create('Subperfil', array('url' => array('controller' => 'subperfil','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('subperfil/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
