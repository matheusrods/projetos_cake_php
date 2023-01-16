<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes','action' => 'logos_cores', $codigo_cliente, (!empty($codigo_matriz))? $codigo_matriz: '', (!empty($referencia))? $referencia : ''), 'type' => 'post')); ?>
<?php echo $this->element('clientes/imagens_pos', array('edit_mode' => true)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>
