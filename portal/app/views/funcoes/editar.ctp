<?php echo $this->BForm->create('Funcao', array('url' => array('controller' => 'funcoes', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('funcoes/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>