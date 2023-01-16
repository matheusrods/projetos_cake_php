

<div id="cliente" class='well'>
    <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
    <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    <strong>Viagens: </strong><span id="qtd-viagens">0</span>
    <strong>Normal: </strong><span id="viagens-normal">0</span>
    <strong>Atrasado: </strong><span id="viagens-atrasado">0</span>
    <strong>Muito Atrasado: </strong><span id="viagens-muito-atrasado">0</span>
    <strong>Sem Posicionamento: </strong><span id="viagens-sem-posicionamento">0</span>
		<span class="pull-right">
			<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
		</span>
</div>
<div id="controles" class='actionbar-right'>
	<?= $this->Html->link('<i class="cus-resultset-previous"></i>', 'javascript:transitTimePreviousPage()', array('escape' => false)) ?>
	<?= $this->Html->link('<i class="cus-resultset-next"></i>', 'javascript:transitTimeNextPage()', array('escape' => false)) ?>
</div>
<table class='table'>
	<thead>
		<th style='width:14px'></th>
		<th class='input-small' style="width: 20px;">Placa/SM</th>
		<th colspan="2" style="min-width: 250px;">Origem / Destino / Posicao Atual</th>
		<!-- <th class='input-medium'></th> -->
		<th class='input-medium'>Região 1º Entr.</th>
		<th class='input-medium' style="width: 120px;">Ini Previsto</th>
		<th class='input-medium' style="width: 120px;">Final Previsto</th>
		<th class='input-medium' style="width: 120px;">Ini Real / Loadplan</th>
		<th class='input-small' style="min-width: 96px;">Status</th>		
	</thead>
	<tbody id='transit-time' style='min-height:60px'>
	</tbody>
</table>
<?php if (isset($cliente) && !empty($cliente['Cliente']['codigo'])): ?>
	<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {transitTimeListagem({$filtros['quantidade_sms']}, {$filtros['intervalo']})})") ?>
<?php endif ?>

<?= $javascript->codeBlock('jQuery(window).ready(function($) { 
	$(document).on("mouseenter",".resumo",function(){
    	$(this).tooltip({placement:"top"});
    	$(this).tooltip("show");
	});
});') ?>