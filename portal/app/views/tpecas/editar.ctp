<?php echo $this->BForm->create('Tpecas', array('url' => array('controller' => 'tpecas','action' => 'editar', $this->passedArgs[0]), 'enctype' => 'multipart/form-data')); ?>
<?php echo $this->element('tpecas/fields', array('edit_mode' => true)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 