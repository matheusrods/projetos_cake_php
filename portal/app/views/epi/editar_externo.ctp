<?php echo $this->BForm->create('EpiExterno', array('url' => array('controller' => 'epi', 'action' => 'editar_externo/'.$codigo_cliente.'/'.$this->data['Epi']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('epi/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>