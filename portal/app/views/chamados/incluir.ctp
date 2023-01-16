<?php echo $this->BForm->create('Chamado', array('url' => array('controller' => 'chamados','action' => 'incluir'))); ?>
<?php echo $this->element('chamados/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>