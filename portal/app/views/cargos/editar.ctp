<?php echo $this->BForm->create('Cargo', array('url' => array('controller' => 'cargos', 'action' => 'editar', $codigo_cliente, $this->data['Cargo']['codigo'], $referencia, $terceiros_implantacao), 'type' => 'post')); ?>
<?php echo $this->element('cargos/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>