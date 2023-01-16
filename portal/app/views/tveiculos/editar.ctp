<?php echo $this->BForm->create('Tveiculos', array('url' => array('controller' => 'tveiculos','action' => 'editar', $this->passedArgs[0]), 'enctype' => 'multipart/form-data')); ?>
<?php echo $this->element('tveiculos/fields', array('edit_mode' => true)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 