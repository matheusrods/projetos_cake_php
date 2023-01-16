<?php echo $this->BForm->create('AtribuicaoCargo', array('url' => array('controller' => 'atribuicoes_cargos', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('atribuicoes_cargos/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>