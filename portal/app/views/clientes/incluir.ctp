
<?php if($terceiros_implantacao == 'terceiros_implantacao'): ?>
	<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes','action' => 'incluir', (!empty($codigo_matriz))? $codigo_matriz: '', (!empty($referencia))? $referencia : '', $terceiros_implantacao), 'type' => 'post')); ?>
<?php else: ?>
	<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes','action' => 'incluir', (!empty($codigo_matriz))? $codigo_matriz: '', (!empty($referencia))? $referencia : ''), 'type' => 'post')); ?>
<?php endif; ?>

	<?php if(isset($codigo_matriz) || isset($this->data['GrupoEconomicoCliente']['codigo_grupo_cliente'])) : ?>
		<?php echo $this->BForm->hidden('codigo_grupo', array('value' => $codigo_matriz ? $codigo_matriz : $this->data['GrupoEconomicoCliente']['codigo_grupo_cliente'])); ?>
	<?php endif; ?>

<?php echo $this->element('clientes/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>