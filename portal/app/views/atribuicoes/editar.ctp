<?php echo $this->BForm->create('Atribuicao', array('url' => array('controller' => 'atribuicoes', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('atribuicoes/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>