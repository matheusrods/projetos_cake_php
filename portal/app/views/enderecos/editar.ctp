<?php echo $this->BForm->create('Endereco', array('action' => 'editar', $this->passedArgs[0] )); ?>
<?php echo $this->element('enderecos/fields', array('edit_mode' => true)); ?>
<?php //echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?>