<?php 

if ($bloquear == true) {
	echo $this->BForm->create('Fornecedores', array('url' => array('controller' => 'fornecedores','action' => 'editar', $codigo_fornecedor, 'true'), 'type' => 'post', 'enctype' => 'multipart/form-data'));
} else {
	echo $this->BForm->create('Fornecedores', array('url' => array('controller' => 'fornecedores','action' => 'editar', $codigo_fornecedor), 'type' => 'post', 'enctype' => 'multipart/form-data'));
}

?>
	<?php echo $this->element('fornecedores/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>
