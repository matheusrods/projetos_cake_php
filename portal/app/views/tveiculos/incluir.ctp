<?php echo $this->BForm->create('Tveiculos',array('url' => array('controller' => 'tveiculos','action' => 'incluir'), 'type' => 'POST', 'enctype' => 'multipart/form-data')) ?>
<?php echo $this->element('tveiculos/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 