<?php echo $this->BForm->create('EpcExterno', array('url' => array('controller' => 'epc', 'action' => 'editar_externo/'.$codigo_cliente.'/'.$this->data['Epc']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('epc/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>