<?php echo $this->BForm->create('Atribuicao', array('url' => array('controller' => 'atribuicoes','action' => 'incluir',  $this->data['Atribuicao']['codigo_cliente']))); ?>
<?php echo $this->element('atribuicoes/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>