<?php echo $this->BForm->create('MotivosAcrescimo', array('url' => array('controller' => 'motivos_acrescimo','action' => 'incluir'))); ?>
<?php echo $this->element('motivos_acrescimo/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>