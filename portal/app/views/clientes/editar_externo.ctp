<?php echo $this->BForm->create('ClienteExterno', array('url' => array('controller' => 'clientes', 'action' => 'editar_externo/'.$codigo_matriz.'/'.$this->data['Cliente']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('clientes/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>