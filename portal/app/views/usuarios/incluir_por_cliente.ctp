<?php echo $this->BForm->create('Usuario', array('url' => array('action' => 'incluir_por_cliente', $this->passedArgs[0]))); ?>
<?php echo $this->element('usuarios/fields_por_cliente'); ?>