<?php echo $this->BForm->create('GrupoHomogeneo', array('url' => array('controller' => 'grupos_homogeneos', 'action' => 'editar', $codigo_cliente, $codigo, $referencia), 'type' => 'post')); ?>
<?php echo $this->element('grupos_homogeneos/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>