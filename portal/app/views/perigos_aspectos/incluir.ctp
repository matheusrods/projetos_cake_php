<?php echo $this->BForm->create('PerigosAspectos', array('url' => array('controller' => 'perigos_aspectos','action' => 'incluir'))); ?>
<?php echo $this->element('perigos_aspectos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
