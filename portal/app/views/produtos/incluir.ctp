<?php echo $this->BForm->create('Produto', array('action' => 'incluir')); ?>
<?php echo $this->element('produtos/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>