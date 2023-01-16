<?php echo $this->BForm->create('AtribuicaoExame', array('url' => array('controller' => 'atribuicoes_exames','action' => 'incluir',  $this->data['AtribuicaoExame']['codigo_cliente']))); ?>
<?php echo $this->element('atribuicoes_exames/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>