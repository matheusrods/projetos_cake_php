<?php echo $this->BForm->create('Tpecas',array('url' => array('controller' => 'tpecas','action' => 'incluir'), 'type' => 'POST', 'enctype' => 'multipart/form-data')) ?>
<?php echo $this->element('tpecas/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 