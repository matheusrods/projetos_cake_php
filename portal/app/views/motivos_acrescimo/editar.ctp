<?php echo $this->BForm->create('MotivosAcrescimo', array('url' => array('controller' => 'motivos_acrescimo', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('motivos_acrescimo/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>