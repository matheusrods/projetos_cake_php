<?php echo $this->BForm->create('AtribuicaoExame', array('url' => array('controller' => 'atribuicoes_exames', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('atribuicoes_exames/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>