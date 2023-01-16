<?php ($mescla = $this->data['nivel_de_servicos']['mesclar_prazo_adiantado']) ?>
<?php if(empty($dados)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
	<?php $eixo_x = array() ?>
	<?php $adiantado = array() ?>
	<?php $no_prazo = array() ?>
	<?php $atrasado = array() ?>
	<?php $total = array() ?>
	<?php $sms = array() ?>
	<?php $porcentagem = array() ?>
	<div class='well'>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    </div>
    <h4>Efetividade</h4>
	<div class='row-fluid' id="grafico-nivel-servico"></div>
	<?php if ($tem_pcp): ?>
		<div class='row-fluid' id="grafico-nivel-servico-base">
			<div class='actionbar-right'>
				<?php echo $this->Html->link('Motivos de Atraso', 'javascript:void(0)', array('id' => 'motivos-atraso')); ?>
			</div>
		</div>
	<?php endif ?>
	<?php echo $this->Javascript->codeBlock("function filtros() {return ".json_encode($this->data['nivel_de_servicos'])."}") ?>
	<table class='table table-striped nivel-servico'>
		<thead>
			<tr>
				<th class='input-small'><?= $this->Html->link($agrupamento[$this->data['nivel_de_servicos']['agrupamento']], 'javascript:void(0)') ?></th>
				<th class='numeric input-small'><?= $this->Html->link('SMs', 'javascript:void(0)') ?></th>
					<?php if($mescla == 1 ): ?>
						<th class='numeric input-small'><?= $this->Html->link('No Prazo', 'javascript:void(0)') ?></th>
					<?php endif ?>
					<?php if(!isset($mescla) || $mescla == 0):?>
						<th class='numeric input-small'><?= $this->Html->link('Adiantado', 'javascript:void(0)') ?></th>
						<th class='numeric input-small'><?= $this->Html->link('No Prazo', 'javascript:void(0)') ?></th>
					<?php endif ?>
				<th class='numeric input-small'><?= $this->Html->link('Atrasado', 'javascript:void(0)') ?></th>
				<th class='numeric input-small'><?= $this->Html->link('Total', 'javascript:void(0)') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $total_sm = 0 ?>
			<?php $total_adiantado = 0 ?>
			<?php $total_no_prazo = 0 ?>
			<?php $total_atrasado = 0 ?>
			<?php $total_mesclado = 0 ?>
			<?php foreach ($dados as $dado): ?>
					<?php if($mescla == 1 ): ?>
						<?php $total_linha = $dado[0]['qtd_atrasado'] + $dado[0]['mesclar_prazo_adiantado']?>
					<?php endif ?>
					<?php if(!isset($mescla) || $mescla == 0): ?>
						<?php $total_linha = $dado[0]['qtd_adiantado'] + $dado[0]['qtd_no_prazo'] + $dado[0]['qtd_atrasado'] ?>
					<?php endif ?>
				<?php $eixo_x[] = "'".($this->data['nivel_de_servicos']['descricao_grafico'] == 2 && in_array($this->data['nivel_de_servicos']['agrupamento'], array(1,4)) ? ($dado[0]['descricao_externo_embarcador'] ? $dado[0]['descricao_externo_embarcador'] : $dado[0]['descricao_externo_transportador']) : str_replace($dado[0]['descricao_agrupamento'], "'", ' ') )."'" ?>
				<?php $sms[] = $dado[0]['qtd_sm'] ?>
				<?php $adiantado[] = $dado[0]['qtd_adiantado'] ?>
				<?php $no_prazo[] = $dado[0]['qtd_no_prazo'] ?>
				<?php $atrasado[] = $dado[0]['qtd_atrasado'] ?>
				<?php $total[] = $total_linha ?>
					<?php if($mescla == 1 ): ?>
						<?php $mesclado = $dado[0]['mesclar_prazo_adiantado'] ?>
					<?php endif ?>

					<?php if($mescla == 1 ): ?>
						<?php $porcentagem[] = round($dado[0]['mesclar_prazo_adiantado'] / ($total_linha) * 100,2) ?>
					<?php endif ?>
					<?php if(!isset($mescla) || $mescla == 0): ?>
						<?php $porcentagem[] = round($dado[0]['qtd_no_prazo'] / ($total_linha) * 100,2) ?>
					<?php endif ?>
				<?php $total_sm += $dado[0]['qtd_sm'] ?>
					<?php if($mescla == 1 ): ?>
						<?php $total_mesclado += $dado[0]['mesclar_prazo_adiantado'] ?>
					<?php endif ?>
					<?php if(!isset($mescla) || $mescla == 0): ?>
						<?php $total_adiantado += $dado[0]['qtd_adiantado'] ?>
						<?php $total_no_prazo += $dado[0]['qtd_no_prazo'] ?>
					<?php endif ?>
				<?php $total_atrasado += $dado[0]['qtd_atrasado'] ?>
				<tr>
					<td><?= $dado[0]['descricao_agrupamento'] ?></td>
					<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($dado[0]['qtd_sm'], array('places' => 0, 'nozero' => true)), 'javascript:void(0)', array('onclick'=>"abreRelatorioSm('{$this->data['nivel_de_servicos']['data_inicial']}', '{$this->data['nivel_de_servicos']['data_final']}', {$this->data['nivel_de_servicos']['codigo_cliente']}, {$this->data['nivel_de_servicos']['base_cnpj']}, {$this->data['nivel_de_servicos']['agrupamento']}, {$dado[0]['codigo_agrupamento']})")) ?></td>
						<?php if($mescla == 1 ): ?>
							<td class='numeric input-medium'>
								<?= $this->Html->link($this->Buonny->moeda($dado[0]['mesclar_prazo_adiantado'], array('places' => 0, 'nozero' => true)), 'javascript:void(0);', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '{$dado[0]['codigo_agrupamento']}', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."', ".TVlocViagemLocal::STATUS_JANELA_NO_PRAZO.")" )); ?>
								(<?= $this->Buonny->moeda($dado[0]['mesclar_prazo_adiantado'] / ($total_linha) * 100).'%' ?>)
							</td>
						<?php endif ?>
						<?php if(!isset($mescla) || $mescla == 0): ?>
							<td class='numeric input-medium'>
								<?= $this->Html->link($this->Buonny->moeda($dado[0]['qtd_adiantado'], array('places' => 0, 'nozero' => true)), 'javascript:void(0);', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '{$dado[0]['codigo_agrupamento']}', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."', ".TVlocViagemLocal::STATUS_JANELA_ADIANTADO.")" )); ?>
								(<?= $this->Buonny->moeda($dado[0]['qtd_adiantado'] / ($total_linha) * 100).'%' ?>)
							</td>
							<td class='numeric input-medium'>
								<?= $this->Html->link($this->Buonny->moeda($dado[0]['qtd_no_prazo'], array('places' => 0, 'nozero' => true)), 'javascript:void(0);', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '{$dado[0]['codigo_agrupamento']}', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."', ".TVlocViagemLocal::STATUS_JANELA_NO_PRAZO.")" )); ?>
								(<?= $this->Buonny->moeda($dado[0]['qtd_no_prazo'] / ($total_linha) * 100).'%' ?>)
							</td>
						<?php endif ?>
					<td class='numeric input-medium'>
						<?= $this->Html->link($this->Buonny->moeda($dado[0]['qtd_atrasado'], array('places' => 0, 'nozero' => true)), 'javascript:void(0);', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '{$dado[0]['codigo_agrupamento']}', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."', ".TVlocViagemLocal::STATUS_JANELA_ATRASADO.")" )); ?>
						(<?= $this->Buonny->moeda($dado[0]['qtd_atrasado'] / ($total_linha) * 100).'%' ?>)
					</td>
    				<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($total_linha, array('places' => 0, 'nozero' => true)), 'javascript:void(0);', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '{$dado[0]['codigo_agrupamento']}', '', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."')" )); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<?php if($mescla == 1 ): ?>
				<?php $total_geral =  +$total_mesclado + $total_atrasado; ?>
			<?php endif ?>	
			<?php if(!isset($mescla) || $mescla == 0): ?>
				<?php $total_geral = $total_adiantado + $total_no_prazo + $total_atrasado; ?>
			<?php endif ?>
			<tr>
				<td>Total</td>
				<td class='numeric input-small'><?= $this->Html->link($total_sm, 'javascript:void(0)', array('onclick'=>"abreRelatorioSm('{$this->data['nivel_de_servicos']['data_inicial']}', '{$this->data['nivel_de_servicos']['data_final']}', {$this->data['nivel_de_servicos']['codigo_cliente']}, {$this->data['nivel_de_servicos']['base_cnpj']})")) ?></td>
    			<?php if($mescla == 1 ): ?>
	    			<td class='numeric input-medium'>
	    				<?= $this->Html->link($total_mesclado, 'javascript:void(0);', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."', '".TVlocViagemLocal::STATUS_JANELA_NO_PRAZO."')" )); ?>
	    				(<?= $this->Buonny->moeda($total_mesclado / ($total_geral) * 100).'%' ?>)
	    			</td>
	    		<?php endif ?>
				<?php if(!isset($mescla) || $mescla == 0): ?>
	    			<td class='numeric input-medium'>
	    				<?= $this->Html->link($total_adiantado, 'javascript:void(0);', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."', ".TVlocViagemLocal::STATUS_JANELA_ADIANTADO.")" )); ?>
	    				(<?= $this->Buonny->moeda($total_adiantado / ($total_geral) * 100).'%' ?>)
	    			</td>
	    			<td class='numeric input-medium'>
	    				<?= $this->Html->link($total_no_prazo, 'javascript:void(0);', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."', '".TVlocViagemLocal::STATUS_JANELA_NO_PRAZO."')" )); ?>
	    				(<?= $this->Buonny->moeda($total_no_prazo / ($total_geral) * 100).'%' ?>)
	    			</td>
	    		<?php endif ?>
    			<td class='numeric input-medium'>
    				<?= $this->Html->link($total_atrasado, 'javascript:void(0);', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."', ".TVlocViagemLocal::STATUS_JANELA_ATRASADO.")" )); ?>
    				(<?= $this->Buonny->moeda($total_atrasado / ($total_geral) * 100).'%' ?>)
    			</td>
    			<td class='numeric input-small'><?= $this->Html->link($total_geral, 'javascript:void(0);', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."')" )); ?></td>
			</tr>
		</tfoot>
	</table>
	<?php 
	if($mescla == 1 ) {
		$adiantado = $mesclado;

	}
	$series = array(
		array('name' => "'".'No Prazo'."'", 'values' => $no_prazo, 'color' => '#009900'),
		array('name' => "'".'Atrasado'."'", 'values' => $atrasado, 'color' => '#CC0000'),
		array('name' => "'".'Total'."'", 'values' => $total, 'color' => '#0000CC'),
		array(
			'name' => "'".'%No Prazo'."'", 
			'values' => $porcentagem, 
			'color' => '#333333', 
			'type' => 'line', 
			'dataLabels' => array(
				'enabled' => true,
				'formatter' => "function(){return this.y+'%';}"
			),
			'marker' => array(
				'enabled' => 'false',
			),
		),
	);

	if($mescla == 0) {
		$no_prazo_array = array('name' => "'".'Adiantado'."'", 'values' => $adiantado, 'color' => '#ffd800');
		array_push($series, $no_prazo_array);
	}

	?>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
	        'renderTo' => 'grafico-nivel-servico',
	        'chart' => array('type' => 'column', 'spacingBottom' => 70),
	        'yAxis' => array('title' => false),
	        'xAxis' => array('labels' => array('rotation' => -75, 'y' => 30), 'gridLineWidth' => 1),
	        'exporting' => array('buttons' => array('exportButton' => array('enabled' => 'false'), 'printButton' => array('enabled' => 'false')),),
	        'tooltip' => array('formatter' => "'<b>'+ this.series.name +'</b><br/>'+this.y"),
	    )));
	?>	
<?php endif ?>
<?php if ($tem_pcp): ?>
	<?php echo $this->Javascript->codeBlock('jQuery("#motivos-atraso").click(function(){
		bloquearDiv($(this));
		$.ajax({
			type: "POST",
			url: baseUrl + "viagens/nivel_de_servicos_motivos_atraso/" + Math.random(),
			dataType: "html",
			data: '.json_encode(array('TViagViagem' => $this->data['nivel_de_servicos'])).',
			beforeSend: function() {
				bloquearDiv($("#grafico-nivel-servico-base"));
			},
			success: function(data) {
				$("#grafico-nivel-servico-base").html(data);
			},
			complete: function() {
				$("#grafico-nivel-servico-base").unblock();
			}
		});
	});', false); ?>
<?php endif ?>