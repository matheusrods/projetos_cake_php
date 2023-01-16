<?php echo $this->BForm->create('RiscoAtributoDetalhe', array('url' => array('controller' => 'riscos_atributos_detalhes', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('riscos_atributos_detalhes/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>