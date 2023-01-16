<?php echo $this->BForm->create('Regulador',array('url' => array('controller' => 'reguladores','action' => 'incluir'), 'type' => 'POST')) ?>
<?php echo $this->element('reguladores/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 