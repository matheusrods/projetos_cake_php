<?php echo $this->BForm->create('TTtraTipoTransporte', array('url'=>array('controller' => 'tipo_transportes', 'action' => 'incluir'))); ?>
<?php echo $this->element('tipo_transportes/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>