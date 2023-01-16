<?php echo $this->BForm->create('FornecedorDocumento', array('type' => 'file', 'enctype' => 'multipart/form-data', 'url' => array('controller' => 'fornecedores_documentos','action' => 'incluir',$codigo_fornecedor), 'onSubmit' => 'return false;')); ?>
<?php echo $this->element('fornecedores_documentos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>

<?php $this->addScript($this->Buonny->link_js('fornecedores.js')); ?>