<?php echo $this->BForm->create('GrupoHomogeneoExame', array('url' => array('controller' => 'grupos_homogeneos_exames', 'action' => 'editar', $codigo_cliente, $codigo, $referencia), 'type' => 'post')); ?>
<?php echo $this->element('grupos_homogeneos_exames/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>