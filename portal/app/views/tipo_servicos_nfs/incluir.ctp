<?php echo $this->BForm->create('TipoServicosNfs', array('url' => array('controller' => 'tipo_servicos_nfs','action' => 'incluir'))); ?>
<?php echo $this->element('tipo_servicos_nfs/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>