<?php echo $this->BForm->create('Prestador',array('url' => array('controller' => 'prestadores','action' => 'incluir'), 'type' => 'POST')) ?>
<?php echo $this->element('prestadores/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 