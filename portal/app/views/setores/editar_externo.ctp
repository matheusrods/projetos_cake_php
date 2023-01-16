<?php echo $this->BForm->create('SetorExterno', array('url' => array('controller' => 'setores', 'action' => 'editar_externo/'.$codigo_cliente.'/'.$this->data['Setor']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('setores/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>