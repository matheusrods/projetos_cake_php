<div class="well">
	<strong>Código: </strong><?php echo $cliente['Cliente']['codigo']; ?>
	<strong>Cliente: </strong><?php echo $cliente['Cliente']['razao_social']; ?>
	<strong>Ano: </strong><?= date('Y'); ?>
</div>

<?php echo $this->BForm->create('ItemPedido', array('url' => array('controller' => 'itens_pedidos', 'action' => 'editar_v2', $codigo_cliente, $codigo_pedido))); ?>
<?php echo $this->element('itens_pedidos/fields_v2', array('edit' => true)); ?>

<?php echo $this->BForm->end(); ?>


<style type="text/css">
	.input-prepend .help-block.error-message{
		font-size: 14px;
		margin-top: 10px;
	}
	.control-group{
		margin-bottom: 0px !important;
	}
	input[type="text"]{
		margin-bottom: 5px !important;
	}
	.app-begin{
		border-top: 2px solid #e2e2e2;
		background: whitesmoke;
	}
	.margin-left-5{
		margin-left: 5px !important;
	}
	label{
		font-weight: bold;
	}
	.div-table div[class^="span"]{
		text-align: right;
		padding-top: 7px;
	}
	.div-table div[class^="span"]:first-child{
		text-align: left;
	}
	.div-table div[class^="span"]:last-child{
		padding-top: inherit;
		text-align: inherit;
	}
	.inseridos div.div-table:last-child {
		border-bottom: 1px #ccc solid !important;
	}
	.div-table{
		margin-top: 2px;
		border-top: 1px #ccc solid;
	}
	.inseridos div.div-table:first-child{
		margin-top: 20px;
	}
</style>