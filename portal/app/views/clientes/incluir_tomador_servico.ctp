<?php echo $this->BForm->create('Cliente', array('url' => array('controller' => 'clientes','action' => 'incluir_tomador_servico', (!empty($codigo_matriz))? $codigo_matriz: ''), 'type' => 'post')); ?>
<?php if(isset($codigo_matriz) || isset($this->data['GrupoEconomicoCliente']['codigo_grupo_cliente'])) : ?>
	<?php echo $this->BForm->hidden('codigo_grupo', array('value' => $codigo_matriz ? $codigo_matriz : $this->data['GrupoEconomicoCliente']['codigo_grupo_cliente'])); ?>
<?php endif; ?>

<?php echo $this->element('clientes/fields_tomador', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>