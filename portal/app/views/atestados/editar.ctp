<?php echo $this->BForm->create('Atestado', array('url' => array('controller' => 'atestados','action' => 'editar', $this->passedArgs[0], $this->passedArgs[1], $codigo_atestado), 'enctype' => 'multipart/form-data')); ?>
<?php echo $this->element('atestados/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>
<?php echo $this->BForm->input('codigo_atestado', array('type' => 'hidden', 'value' => $codigo_atestado)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>