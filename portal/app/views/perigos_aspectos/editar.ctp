<?php echo $this->BForm->create('PerigosAspectos', array('url' => array('controller' => 'perigos_aspectos','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('perigos_aspectos/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
