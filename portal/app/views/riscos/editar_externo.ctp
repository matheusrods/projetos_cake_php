<?php echo $this->BForm->create('RiscoExterno', array('url' => array('controller' => 'riscos', 'action' => 'editar_externo/'.$codigo_cliente.'/'.$this->data['Risco']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('riscos/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>