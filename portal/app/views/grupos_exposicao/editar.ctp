<?php echo $this->BForm->create('GrupoExposicao', array('url' => array('controller' => 'grupos_exposicao', 'action' => 'editar', $codigo_cliente, $codigo), 'type' => 'post')); ?>
<?php echo $this->element('grupos_exposicao/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>