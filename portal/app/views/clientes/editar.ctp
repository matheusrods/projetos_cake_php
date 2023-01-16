<?php if($terceiros_implantacao == 'interno'): ?>
	<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes','action' => 'editar', $codigo_cliente, (!empty($codigo_matriz))? $codigo_matriz: '', (!empty($referencia))? $referencia : ''), 'type' => 'post')); ?>
<?php else: ?>
	<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes','action' => 'editar', $codigo_cliente, (!empty($codigo_matriz))? $codigo_matriz: '', (!empty($referencia))? $referencia : '', 'null', $terceiros_implantacao), 'type' => 'post')); ?>
<?php endif; ?>

<?php echo $this->element('clientes/fields', array('edit_mode' => true)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>