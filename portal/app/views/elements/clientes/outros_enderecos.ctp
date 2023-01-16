<div class="row-fluid">
	<span class="span12 span-right">
		<?php echo $html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'clientes_enderecos','action' => 'incluir', $this->data['Cliente']['codigo']), array('escape' => false, 'class' => 'btn btn-success dialog_cliente_endereco', 'title' => 'Incluir Endereço')) ?>
	</span>
</div>
<div id="endereco-cliente" class="grupo"></div>