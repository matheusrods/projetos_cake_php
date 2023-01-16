<?php echo $this->BForm->create('Regulador', array('url' => array('controller' => 'reguladores','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('reguladores/fields', array('edit_mode' => true)); ?>
<?php echo $javascript->codeblock('
	jQuery(document).ready(function() {
		setup_mascaras();			
	});'); 
?>