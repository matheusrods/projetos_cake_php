<?php echo $this->BForm->create('GrupoHomogeneoExterno', array('url' => array('controller' => 'grupos_homogeneos', 'action' => 'editar_externo/'.$codigo_cliente.'/'.$this->data['GrupoHomogeneo']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('grupos_homogeneos/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>