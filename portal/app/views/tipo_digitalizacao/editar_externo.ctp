<?php echo $this->BForm->create('Atribuicao', array('url' => array('controller' => 'atribuicao', 'action' => 'editar_externo/'.$this->data['Atribuicao']['codigo_cliente'].'/'.$this->data['Atribuicao']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('atribuicoes/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>