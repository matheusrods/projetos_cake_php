<?php echo $this->BForm->create('TTveiTipoVeiculo', array('url' => array('controller' => 'tipos_veiculos', 'action' => 'editar', $codigo_tipo))); ?>
<?php echo $this->element('tipos_veiculos/fields'); ?>
<?php echo $this->BForm->end(); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>