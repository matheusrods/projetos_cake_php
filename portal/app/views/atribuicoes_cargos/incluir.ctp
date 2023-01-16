<?php echo $this->BForm->create('AtribuicaoCargo', array('url' => array('controller' => 'atribuicoes_cargos','action' => 'incluir'))); ?>
<?php echo $this->element('atribuicoes_cargos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>