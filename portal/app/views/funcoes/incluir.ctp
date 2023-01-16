<?php echo $this->BForm->create('Funcao', array('url' => array('controller' => 'funcoes','action' => 'incluir'))); ?>
<?php echo $this->element('funcoes/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>