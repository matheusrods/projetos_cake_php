<?php echo $this->BForm->create('GrupoRisco', array('url' => array('controller' => 'grupos_riscos','action' => 'incluir'))); ?>
<?php echo $this->element('grupos_riscos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>