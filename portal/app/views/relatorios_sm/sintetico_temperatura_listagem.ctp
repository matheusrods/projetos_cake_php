<?php if (empty($relatorioListagem)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>

<?php 
if($grafico_transportador) {
	$style = "style='width:100%;height:800px;float:left'" ;
}
else {
	$style = "style='width:100%;height:450px;float:left'" ;
}
?>


<div id="grafico" <?php echo $style ?> ></div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
</br>
	<table class='table table-striped status-alvos'>
	    <thead>
	        <tr>
	            <th><?= $this->Html->link($agrupamento_label, 'javascript:void(0)') ?></th>
	            <th class='numeric'><?= $this->Html->link('% Dentro Temp.', 'javascript:void(0)') ?></th>
	            <th class='numeric'><?= $this->Html->link('% Fora Temp.', 'javascript:void(0)') ?></th>
	            <th class='numeric'><?= $this->Html->link('Total', 'javascript:void(0)') ?></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach($relatorioListagem as $relatorio): ?>
		        <tr>
		            <td><?php echo (trim($relatorio[0]['agrupamento'])!='' ? $relatorio[0]['agrupamento'] : 'Não definido'); ?></td>
					<td class="numeric"><?php echo number_format(!empty($relatorio[0]['vtem_percentual_dentro']) ? $relatorio[0]['vtem_percentual_dentro'] : '0',2,',','.')."%" ?></td>
					<td class="numeric"><?php echo number_format(!empty($relatorio[0]['vtem_percentual_fora']) ? $relatorio[0]['vtem_percentual_fora'] : '100',2,',','.')."%" ?></td>
		            <td class='numeric'><?php echo $this->Html->link($this->Buonny->moeda($relatorio[0]['total'], array('nozero'=>true, 'places'=>0)), array('action'=>'listagem_acompanhamento_viagens_analitico', 'popup' , 0, $relatorio[0]['codigo'], true, 1), array('onclick'=>'return open_popup(this);')); ?></td>
		        </tr>
	        <?php endforeach; ?>  
	    </tbody>
	    <tfoot>
	        <tr>
	        	<td>Total</td>
	            <td class='numeric'><strong><?php echo number_format(!empty($totaisListagem['vtem_percentual_dentro']) ? $totaisListagem['vtem_percentual_dentro'] : '0',2,',','.')."%"; ?></strong></td>
	            <td class='numeric'><strong><?php echo number_format(!empty($totaisListagem['vtem_percentual_fora']) ? $totaisListagem['vtem_percentual_fora'] : '0',2,',','.')."%"; ?></strong></td>
	            <td class='numeric'><strong><?php echo $this->Html->link($this->Buonny->moeda(!empty($totaisListagem['total_geral']) ? $totaisListagem['total_geral'] : '0', array('nozero'=>true, 'places'=>0)), array('action'=>'listagem_acompanhamento_viagens_analitico', 'popup' , 0, 0, 0 , 1), array('onclick'=>'return open_popup(this);')); ?></strong></td>
	        </tr>
	    </tfoot>
	</table>

	<?php echo $this->Javascript->codeBlock(
		$this->Highcharts->render($dadosGrafico['eixo_x'], $dadosGrafico['series'], 
			array(
    				'renderTo' => 'grafico',
    				'chart' => array('type' => 'column'),
    				'yAxis' => array('title' => ''),
    				'xAxis' => array('labels' => array('rotation' => $rotate_angle, 'y' => 10), 'gridLineWidth' => 1),
    				'tooltip' => array('formatter' => 'this.y'),
    				'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'),'printButton' => array('enabled'=> 'false'))),
    				'plotOptions' => array('series' => array('dataLabels' => array (
    					'enabled' => 'true',
    					'color' => '#000000'
    				 )))
				)
			)
		); 
	?>
	<?php echo $this->Javascript->codeBlock('
	    jQuery(document).ready(function(){
			$.tablesorter.addParser({
				debug:true,
				id: "qtd", 
				is: function(s) { 
					// return false so this parser is not auto detected 
					// poderia ser detectado pelo simbolo do real R$
					return false;
				},
				format: function(s) { 
				   return $.tablesorter.formatInt(s.replace(new RegExp(/\(\d*\)/g),""));
				}, 
				type: "numeric"
			});
			
			jQuery("table.status-alvos").tablesorter({
				headers: {
					1: {sorter: "qtd"},
					2: {sorter: "qtd"},
					3: {sorter: "qtd"}
				},
				widgets: ["zebra"]
			});
	    });', false);
	?>
<?php endif; ?>