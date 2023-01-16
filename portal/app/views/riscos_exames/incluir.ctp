<?php echo $this->BForm->create('RiscoExame', array('url' => array('controller' => 'riscos_exames','action' => 'incluir',  $this->data['RiscoExame']['codigo_cliente']))); ?>
<?php echo $this->element('riscos_exames/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
