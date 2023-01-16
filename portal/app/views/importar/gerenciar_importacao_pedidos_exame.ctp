<div class='well'>
	<strong>Nome do arquivo: </strong><?= $pedidos_exame['ImportacaoPedidosExame']['nome_arquivo'] ?>
	<strong>Data inclusão: </strong><?= Comum::formataData($pedidos_exame['ImportacaoPedidosExame']['data_inclusao']) ?>
	<strong>Status: </strong><?php
		if ($pedidos_exame['ImportacaoPedidosExame']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR) echo "Sem processar";
		if ($pedidos_exame['ImportacaoPedidosExame']['codigo_status_importacao'] == StatusImportacao::PROCESSANDO) echo "Em processamento";
		if ($pedidos_exame['ImportacaoPedidosExame']['codigo_status_importacao'] == StatusImportacao::PROCESSADO) echo "Processado";
	?>
	<?php if ($pedidos_exame['ImportacaoPedidosExame']['codigo_status_importacao'] == StatusImportacao::PROCESSADO): ?>
		<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => 'exportar_importacao_pedidos_exame_processada', $pedidos_exame['ImportacaoPedidosExame']['codigo']), array('escape' => false, 'title' =>'Exportar registros não importados', 'style' => 'float:right'));?>
	<?php endif ?>
</div>
<div class='lista' style="min-height: 50px"></div>
<?= $this->BForm->create('ImportacaoPedidosExame', array('url' => array('controller' => 'importar', 'action' => 'gerenciar_importacao_pedidos_exame', $this->passedArgs[0], $this->passedArgs[1]))) ?>
<?php if ($pedidos_exame['ImportacaoPedidosExame']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR): ?>
	<?= $this->BForm->submit('Processar', array('div' => false, 'class' => 'btn btn-primary')) ?>
<?php endif ?>
<?= $this->Html->link('Voltar',array('controller'=>'importar','action'=>'importar_pedido_exame', $this->passedArgs[0]) , array('class' => 'btn')); ?>
<?= $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function(){
	var div = $('.lista');
	bloquearDiv(div);
	div.load('/portal/importar/importacao_pedido_exame_listagem/{$pedidos_exame['ImportacaoPedidosExame']['codigo']}');
})")
?>
