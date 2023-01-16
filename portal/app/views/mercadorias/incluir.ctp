<?php echo $this->BForm->create('TProdProduto', array('url'=>array('controller' => 'mercadorias', 'action' => 'incluir'))); ?>
<?php echo $this->element('mercadorias/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>