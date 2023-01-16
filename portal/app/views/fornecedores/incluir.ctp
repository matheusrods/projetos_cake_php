<?php echo $this->BForm->create('Fornecedores', array('action' => 'incluir')); ?>
<?php echo $this->element('fornecedores/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 