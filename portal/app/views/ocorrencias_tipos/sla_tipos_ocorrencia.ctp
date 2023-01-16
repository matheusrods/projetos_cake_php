<?php if (!empty($dados)): ?>
    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
	<table class="table table-striped table-bordered tablesorter">
		<thead>
			<tr>
				<th><?= $this->Html->link('Tipo de Ocorrência', 'javascript:void(0)') ?></th>
				<th class="numeric"><?= $this->Html->link('Fora do SLA', 'javascript:void(0)') ?></th>
				<th class="numeric"><?= $this->Html->link('Dentro do SLA', 'javascript:void(0)') ?></th>
				<th class="numeric"><?= $this->Html->link('Total', 'javascript:void(0)') ?></th>
			</tr>
		</thead>
		<tbody>
		    <?php $qtd_tipos = 0; ?>
		    <?php $qtd_fora = 0; ?>
		    <?php $qtd_dentro = 0; ?>
			<?php foreach ($dados['series'][0]['values'] as $key => $qtd): ?>
				<tr>
					<td><?php echo str_replace("'","",$dados['eixo_x'][$key]) ?></td>
					<td class="numeric"><?php echo $dados['series'][1]['values'][$key] ?></td>
					<td class="numeric"><?php echo $qtd ?></td>
					<td class="numeric"><?php echo ($dados['series'][1]['values'][$key] + $qtd) ?></td>
				</tr>
				<?php $qtd_tipos += 1; ?>
				<?php $qtd_fora += $dados['series'][1]['values'][$key]; ?>
				<?php $qtd_dentro += $qtd; ?>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
		    <th class="numeric"><?= $qtd_tipos ?></th>
		    <th class="numeric"><?= $qtd_fora ?></th>
		    <th class="numeric"><?= $qtd_dentro ?></th>
		    <th class="numeric"><?= ($qtd_fora + $qtd_dentro) ?></th>
		</tfoot>
	</table>
	<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter()")) ?>
    
	<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
	<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dados['eixo_x'], $dados['series'], array(
	    'renderTo' => 'grafico',
	    'chart' => array('type' => 'bar'),
	    'plotOptions' => array('series' => array('stacking' => 'normal')),
	))); ?>
<?php endif; ?>