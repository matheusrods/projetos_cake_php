<div class="well">
	<strong>Código: </strong><?php echo $cliente['Cliente']['codigo']; ?>
	<strong>Cliente: </strong><?php echo $cliente['Cliente']['razao_social']; ?>
	<strong>Ano: </strong><?= date('Y'); ?>
</div>

<?php echo $this->BForm->create('ItemPedido', array('url' => array('controller' => 'itens_pedidos', 'action' => 'incluir_pedido_v2', $codigo_cliente))); ?>
<?php echo $this->element('itens_pedidos/fields_v2', array('edit' => false)); ?>

<?php echo $this->BForm->end(); ?>

<?php echo $this->Javascript->codeBlock("
	setup_mascaras();
	var i = ".((isset($key2))? $key2+1 : 0 ).";
	var valor_total = ".((isset($valor_total))? $valor_total : 0).";
	", false); ?>
	<?php $this->addScript($this->Buonny->link_js('itens_pedidos.js')); ?>

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