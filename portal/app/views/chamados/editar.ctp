<?php echo $this->BForm->create('Chamado', array('action' => 'editar', $this->passedArgs[0] )); ?>
<?php echo $this->element('chamados/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
