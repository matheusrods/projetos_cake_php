<?php echo $this->BForm->create('GrupoHomogeneo', array('url' => array('controller' => 'grupos_homogeneos','action' => 'incluir', $codigo_cliente, $referencia))); ?>
<?php echo $this->element('grupos_homogeneos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>