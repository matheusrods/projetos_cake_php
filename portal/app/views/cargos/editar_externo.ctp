<?php echo $this->BForm->create('CargoExterno', array('url' => array('controller' => 'cargos', 'action' => 'editar_externo/'.$this->data['Cargo']['codigo_cliente'].'/'.$this->data['Cargo']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('cargos/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>