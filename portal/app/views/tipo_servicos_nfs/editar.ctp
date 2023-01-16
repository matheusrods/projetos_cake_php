<?php echo $this->BForm->create('TipoServicosNfs', array('url' => array('controller' => 'tipo_servicos_nfs', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('tipo_servicos_nfs/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>