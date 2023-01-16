<?php echo $this->BForm->create('ConselhoProfissional', array('url' => array('controller' => 'medicos', 'action' => 'editar_conselho_classe'), 'type' => 'post')); ?>
<?php echo $this->element('conselho_classe/fields_conselho_classe', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>