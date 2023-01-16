<?php echo $this->BForm->create('Seguradora', array('action' => 'incluir')); ?>
<?php echo $this->element('seguradoras/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 