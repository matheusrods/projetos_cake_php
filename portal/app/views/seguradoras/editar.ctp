<?php echo $this->BForm->create('Seguradora', array('action' => 'editar', $this->passedArgs[0] )); ?>
<?php echo $this->element('seguradoras/fields', array('edit_mode' => true)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 