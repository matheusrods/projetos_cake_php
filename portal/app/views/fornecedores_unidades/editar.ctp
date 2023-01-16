<?php echo $this->BForm->create('FornecedorUnidade', array('url' => array('controller' => 'fornecedores_unidades', 'action' => 'editar', $codigo_fornecedor_matriz, $codigo), 'type' => 'post')); ?>
<?php echo $this->element('fornecedores_unidades/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 