<?php echo $this->BForm->create('MotivoAfastamento', array('url' => array('controller' => 'motivos_afastamento','action' => 'incluir'))); ?>
<?php echo $this->element('motivos_afastamento/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>