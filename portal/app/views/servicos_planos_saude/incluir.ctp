<?php echo $this->BForm->create('Servico', array('action' => 'incluir')); ?>
<?php echo $this->element('servicos/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>