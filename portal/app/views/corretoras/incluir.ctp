<?php echo $this->BForm->create('Corretora', array('action' => 'incluir')); ?>
<?php echo $this->element('corretoras/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 