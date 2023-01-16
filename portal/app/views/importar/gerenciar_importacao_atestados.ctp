<div class='well'>
	<strong>Nome do arquivo: </strong><?= $atestados['ImportacaoAtestados']['nome_arquivo'] ?>
	<strong>Data inclusão: </strong><?= $atestados['ImportacaoAtestados']['data_inclusao'] ?>
	<strong>Status: </strong><?php
		if ($atestados['ImportacaoAtestados']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR) echo "Sem processar";
		if ($atestados['ImportacaoAtestados']['codigo_status_importacao'] == StatusImportacao::PROCESSANDO) echo "Em processamento";
		if ($atestados['ImportacaoAtestados']['codigo_status_importacao'] == StatusImportacao::PROCESSADO) echo "Processado";
	?>
	<?php if ($atestados['ImportacaoAtestados']['codigo_status_importacao'] == StatusImportacao::PROCESSADO): ?>
		<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => 'exportar_importacao_atestados_processada', $atestados['ImportacaoAtestados']['codigo']), array('escape' => false, 'title' =>'Exportar registros não importados', 'style' => 'float:right'));?>
	<?php endif ?>
</div>
<div class='lista' style="min-height: 50px"></div>
<?= $this->BForm->create('ImportacaoAtestados', array('url' => array('controller' => 'importar', 'action' => 'gerenciar_importacao_atestados', $this->passedArgs[0], $this->passedArgs[1]))) ?>
<?php if ($atestados['ImportacaoAtestados']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR): ?>
	<?= $this->BForm->submit('Processar', array('div' => false, 'class' => 'btn btn-primary')) ?>
<?php endif ?>
<?= $this->Html->link('Voltar',array('controller'=>'importar','action'=>'importar_atestado', $this->passedArgs[0]) , array('class' => 'btn')); ?>
<?= $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function(){
	var div = $('.lista');
	bloquearDiv(div);
	div.load('/portal/importar/importacao_atestados_listagem/{$atestados['ImportacaoAtestados']['codigo']}');
})")
?>
