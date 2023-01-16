<?php echo $this->BForm->create('MotivoRecusa', array('url' => array('controller' => 'motivos_recusa','action' => 'incluir'))); ?>
<?php echo $this->element('motivos_recusa/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>