<?php if(empty($filtros['RelatorioSm'])): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php else: ?>
	<div class="well">
		<?php if(!empty($cliente)): ?>
			<strong>Código: </strong><?= $cliente['cliente']['Cliente']['codigo'] ?>
				<strong>Cliente: </strong><?= $cliente['cliente']['Cliente']['razao_social'] ?>
		<?php endif; ?>
		<div class="span3"><strong>De: </strong><?=$filtros['RelatorioSm']['data_inicial']?> <strong>Até:</strong> <?=$filtros['RelatorioSm']['data_final']?></div>
		<div class="span3"><strong>Última atualização: </strong> <?php echo date('d/m/Y H:i:s') ?></div>
		<div class="span3"><strong>Total de Sms: </strong><span id='total-sm'></span></div>
		<span class="pull-right">
			<?php echo $html->link('Atualizar', 'javascript:atualizaListaRelatorioSmAcompanhamentoViagensSintetico();') ?>
		</span>
	</div>
	<div class="row-fluid">
		<div class="span6" style="min-height: 200px">
			<h4>Tipos Veículos</h4>
			<div id="relatorio-tipo-veiculo">
			</div>
		</div>
		<div class="span6" style="min-height: 200px">
			<h4>Status SM</h4>
			<div id="relatorio-status-sm">
			</div>
		</div>
	</div>
	
	<div class="row-fluid">
		<h4>Totais de alvos nas viagens, agrupados por <?php echo $agrupamento_label ?></h4>
		<div id="relatorio-status-alvos">
		</div>
	</div>
	<?php echo $this->Javascript->codeBlock('
		jQuery(document).ready(function(){ 

			carrgaListaRelatorioSmAcompanhamentoViagensSinteticoTipoVeiculo(); 
			carrgaListaRelatorioSmAcompanhamentoViagensSinteticoStatusSm(); 
			carrgaListaRelatorioSmAcompanhamentoViagensSinteticoStatusAlvos(); 

		});

	', false); ?>
<?php endif; ?>