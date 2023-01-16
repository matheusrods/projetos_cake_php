<?php echo $this->BForm->create('RiscoAtributoDetalhe', array('url' => array('controller' => 'riscos_atributos_detalhes','action' => 'incluir'))); ?>
<?php echo $this->element('riscos_atributos_detalhes/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>