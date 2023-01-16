<?php echo $this->BForm->create('FornecedorHistorico', array('type' => 'file', 'enctype' => 'multipart/form-data', 'url' => array('controller' => 'fornecedores_historicos','action' => 'incluir',$codigo_fornecedor), 'onSubmit' => 'return false;')); ?>
<?php echo $this->Form->hidden('codigo_fornecedor'); ?>
<?php echo $this->element('fornecedores_historicos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Buonny->link_js('fornecedores.js')); ?>