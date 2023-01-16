<?php echo $this->BForm->create('MotivoAfastamentoExterno', array('url' => array('controller' => 'motivos_afastamento', 'action' => 'editar_externo/'.$codigo_cliente), 'type' => 'post')); ?>
<?php echo $this->element('motivos_afastamento/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>