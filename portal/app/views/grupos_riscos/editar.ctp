<?php echo $this->BForm->create('GrupoRisco', array('url' => array('controller' => 'grupos_riscos', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('grupos_riscos/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>