<?php echo $this->BForm->create('GrupoHomogeneoExame', array('url' => array('controller' => 'grupos_homogeneos_exames','action' => 'incluir', $codigo_cliente, $referencia))); ?>
<?php echo $this->element('grupos_homogeneos_exames/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>