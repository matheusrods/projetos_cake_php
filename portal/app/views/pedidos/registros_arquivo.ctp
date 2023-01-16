<div class='well'>
	<strong>Nome do arquivo: </strong><?= $pedidos['ImportacaoPedidos']['nome_arquivo'] ?>
	<strong>Data inclusão: </strong><?= $pedidos['ImportacaoPedidos']['data_inclusao'] ?>
	<strong>Status: </strong><?php
		if ($pedidos['ImportacaoPedidos']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR) echo "Sem processar";
		if ($pedidos['ImportacaoPedidos']['codigo_status_importacao'] == StatusImportacao::PROCESSANDO) echo "Em processamento";
		if ($pedidos['ImportacaoPedidos']['codigo_status_importacao'] == StatusImportacao::PROCESSADO) echo "Processado";
	?>
	<?php if ($pedidos['ImportacaoPedidos']['codigo_status_importacao'] == StatusImportacao::PROCESSADO): ?>
		<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => 'exportar_importacao_processada', $pedidos['ImportacaoPedidos']['codigo']), array('escape' => false, 'title' =>'Exportar registros não importados', 'style' => 'float:right'));?>
	<?php endif ?>
</div>
<div class='lista' style="min-height: 50px"></div>
<?= $this->BForm->create('ImportacaoPedidos', array('url' => array('controller' => 'pedidos', 'action' => 'registros_arquivo', $this->passedArgs[0], $this->passedArgs[1]))) ?>
<?php if ($pedidos['ImportacaoPedidos']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR): ?>
	<?= $this->BForm->submit('Processar', array('div' => false, 'class' => 'btn btn-primary')) ?>
<?php endif ?>
<?= $this->Html->link('Voltar',array('controller'=>'pedidos','action'=>'importar', $this->passedArgs[0]) , array('class' => 'btn')); ?>
<?= $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function(){
	var div = $('.lista');
	bloquearDiv(div);
	div.load('/portal/pedidos/listagem_registros/{$pedidos['ImportacaoPedidos']['codigo']}');
})")
?>
