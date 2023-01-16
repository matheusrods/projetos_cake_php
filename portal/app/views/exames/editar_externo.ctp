<?php echo $this->BForm->create('ExameExterno', array('url' => array('controller' => 'exames', 'action' => 'editar_externo/'.$codigo_cliente.'/'.$this->data['Exame']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('exames/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>