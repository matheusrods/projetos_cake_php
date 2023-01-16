<?php echo $this->BForm->create('proprietarios', array('action' => 'incluir')); ?>
<?php echo $this->element('proprietarios/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock(
	'jQuery(document).ready(function() {
		setup_datepicker();
		setup_mascaras();
	});');
?>