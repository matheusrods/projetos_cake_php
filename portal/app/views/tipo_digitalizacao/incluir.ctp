<?php echo $this->BForm->create('TipoDigitalizacao', array('url' => array('controller' => 'TipoDigitalizacao','action' => 'incluir'))); ?>
<?php echo $this->element('tipo_digitalizacao/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>