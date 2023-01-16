<?php echo $this->BForm->create('RiscoAtributoDetalheExterno', array('url' => array('controller' => 'riscos_atributos_detalhes', 'action' => 'editar_externo/'.$codigo_cliente.'/'.$this->data['RiscoAtributoDetalhe']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('riscos_atributos_detalhes/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>