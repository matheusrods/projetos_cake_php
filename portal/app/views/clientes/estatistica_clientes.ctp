<div class='well'>
	<?php echo $this->BForm->create('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes', 'action' => 'estatistica_clientes'))) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('ano', array('label' => false, 'class' => 'input-medium', 'options' => $anos)) ?>
		<?php //echo $this->BForm->input('produto', array('label' => false, 'class' => 'input-xlarge', 'options' => $produtos,  'empty' => 'Selecione um produto')) ?>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end() ?>
	</div>
</div>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php if (!empty($dadosGrafico)): ?>
    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
	<table class="table table-striped table-bordered">
		<thead>
			<th></th>
			<?php foreach ($meses as $mes): ?>
				<th class="numeric"><?php echo $mes ?></th>
			<?php endforeach; ?>
			<th>Total</th>
		</thead>
		<tbody>
			<?php $linha_impar = array(); ?>
			<?php $linha_par   = array(); ?>
			<?php $meses = Comum::listMeses(true); ?>
			<?php for($i = 0; $i < count($dadosGrafico['series']); $i++): ?>
				<?php foreach ($dadosGrafico['series'][$i]['values'] as $key => $value) {
					if(is_null($value)) $dadosGrafico['series'][$i]['values'][$key] = 0;
				} ?>
				<tr>
					<?php $pre_texto = ($i <= 1 ? 'Qt.' : '') ?>
					<td><?php echo $pre_texto.trim($dadosGrafico['series'][$i]['name'], "'"); ?></td>
					<?php $dadosGrafico['series'][$i]['values'] = count($dadosGrafico['series'][$i]['values']) <> 12 ? array_merge($dadosGrafico['series'][$i]['values'], array_fill(0, 12 - count($dadosGrafico['series'][$i]['values']), 0)) : $dadosGrafico['series'][$i]['values']; ?>
					<?php
						if (!in_array($i, array(1,3,5)))
							$linha_par   = $dadosGrafico['series'][$i]['values'];
						else
							$linha_impar = $dadosGrafico['series'][$i]['values'];
					?>
					<?php $total = 0 ?>
					<?php foreach($dadosGrafico['series'][$i]['values'] as $chave => $valor): ?>
						<?php $ano_cadastro = substr($dadosGrafico['series'][$i]['name'],7,4); ?>
						<td class="numeric">
							<?php if ($i<=1): ?>
								<?php $mes = $chave + 1 ?>
								<?= $this->Html->link($this->Buonny->moeda($valor, array('nozero' => true, 'places' => 0)), 'javascript:void(0)', array('onclick' => "faturamento_cliente_produto('{$ano_cadastro}','{$mes}')")); ?>
							<?php else: ?>
								<?= $this->Buonny->moeda($valor, array('nozero' => true, 'places' => 0)); ?>
							<?php endif ?>
						</td>
						<?php $total += $valor ?>
					<?php endforeach; ?>
					<?php
						if (!in_array($i, array(1,3,5)))
							$total_par   = $total;
						else
							$total_impar = $total;
					?>
					<td class="numeric">
						<?php if ($i >= 4): ?>
							<?php echo $total ? round($total / count($dadosGrafico['series'][$i]['values'])): ''; ?>
						<?php else: ?>
							<?php if ($i<=1): ?>
								<?php echo $total ? $this->Html->link($total, 'javascript:void(0)', array('onclick' => "faturamento_cliente_produto('{$ano_cadastro}','')")): ''; ?>
							<?php else: ?>
								<?php echo $total ? $total: ''; ?>
							<?php endif ?>
						<?php endif ?>
					</td>
				</tr>
				<?php if ($i <= 1 && $permissao): ?>
					<tr>
						<?php $pre_texto = 'Vr.' ?>
						<td><?php echo $pre_texto.trim($dadosGrafico['series'][$i]['name'], "'"); ?></td>
						<?php foreach($dadosGrafico['series'][$i]['values'] as $chave => $valor): ?>
							<td class='numeric'>
								<?php $mes = $chave + 1 ?>
								<?= $this->Buonny->moeda($total_faturado[$i][0][0][$meses[$mes]], array('nozero' => true)) ?>
							</td>
						<?php endforeach?>
						<td class='numeric'><?= $this->Buonny->moeda($total_faturado[$i][0][0]['total'], array('nozero' => true)) ?></td>
					</tr>
				<?php endif ?>
				<?php if (in_array($i, array(1,3,5))): ?>
					<tr>
						<td>Variação percentual</td>
						<?php foreach($linha_par as $chave => $num): ?>
							<?php $diferenca = $num && $linha_impar[$chave] ? (($num - $linha_impar[$chave]) * 100 / $num) * -1: 0 ?>
							<td class="numeric" style="<?= $diferenca <= 0 ? 'color:#F00': 'color:#00F' ?>">
								<?php echo $diferenca ? $this->Buonny->moeda($diferenca): ''; ?>
							</td>
						<?php endforeach; ?>
						<?php $diferenca_total = $total_par ? (($total_par - $total_impar) * 100 / $total_par) * -1: 0 ?>
						<td class="numeric" style="<?= $diferenca_total <= 0 ? 'color:#F00': 'color:#00F' ?>"><?php echo $diferenca_total ? $this->Buonny->moeda($diferenca_total): ''; ?></td>
					</tr>
				<?php endif; ?>
				<?php if (in_array($i, array(1,3))):?>
					<tr><td></td></tr>
				<?php endif; ?>
			<?php endfor; ?>
		</tbody>
	</table>
    <?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter();")) ?>
    <?php echo $this->Javascript->codeBlock("
    	function faturamento_cliente_produto(ano, mes_cadastro_cliente) {
    		var field = null;
    		var form = document.createElement('form');
	        var form_id = ('formresult' + Math.random()).replace('.','');
			form.setAttribute('method', 'post');
			form.setAttribute('target', form_id);
	        form.setAttribute('action', '/portal/notas_fiscais/faturamento_anual_cliente_produto/0/' + Math.random());
	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Notafis][ano]');
	        field.setAttribute('value', ano);
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);
	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Notafis][mes_cadastro_cliente]');
	        field.setAttribute('value', mes_cadastro_cliente);
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);
	        var janela = window_sizes();
		    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	        document.body.appendChild(form);
	        form.submit();
    	}
    ") ?>
    
    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
    <?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGrafico['eixo_x'], $dadosGrafico['series'], array(
        'renderTo' => 'grafico',
        'chart' => array('type' => 'line'),
        'yAxis' => array('title' => ''),
        'xAxis' => array('labels' => array('rotation' => -10, 'y' => 20), 'gridLineWidth' => 1),
        'tooltip' => array('formatter' => 'this.y'),
    ))); ?>
<?php endif; ?>