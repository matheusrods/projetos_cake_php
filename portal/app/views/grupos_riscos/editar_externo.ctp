<?php echo $this->BForm->create('GrupoRiscoExterno', array('url' => array('controller' => 'grupos_riscos', 'action' => 'editar_externo/'.$codigo_cliente.'/'.$this->data['GrupoRisco']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('grupos_riscos/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>