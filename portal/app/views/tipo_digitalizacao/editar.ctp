<?php echo $this->BForm->create('TipoDigitalizacao', array('url' => array('controller' => 'tipo_digitalizacao', 'action' => 'editar', $codigo), 'type' => 'post')); ?>
<?php echo $this->Form->hidden('codigo'); ?>
<?php echo $this->element('tipo_digitalizacao/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>