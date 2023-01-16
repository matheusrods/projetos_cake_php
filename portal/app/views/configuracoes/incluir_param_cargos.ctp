<?php echo $this->BForm->create('ClienteConfiguracao', array('url' => array('controller' => 'configuracoes','action' => 'incluir_param_cargos'))); ?>
<?php echo $this->element('configuracoes/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>