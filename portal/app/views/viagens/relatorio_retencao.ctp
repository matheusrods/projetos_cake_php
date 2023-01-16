<?php if(isset($this->passedArgs[0]) && $this->passedArgs[0] == 'export_cd'):


	header('Content-type: application/vnd.ms-excel');
	header(sprintf('Content-Disposition: attachment; filename="%s"', basename('tempo_retencao.csv')));
	header('Pragma: no-cache');

	echo iconv('UTF-8', 'ISO-8859-1', '"CD";"Tempo Médio"')."\n";
	$total_tempo_total = 0;
	$total_quantidade_entregas = 0;
	foreach ($dados as $dado){
		if ($dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregue'] > 0){
			$total_tempo_total += $dado[0]['tempo_total'];
			$total_quantidade_entregas += $dado[0]['quantidade_entregas'];

	    	$linha  = '"'.$dado[0]['descricao_agrupamento'].'";';
	    	$linha .= '"'.Comum::convertToHoursMins($dado[0]['tempo_medio']).'";';
			$linha .= "\n";
		    echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
 			}
 		}
 		die;
endif;
 ?>
<?php
 	$filtrado = (isset($this->data['TViagViagem']['codigo_cliente']) && $this->data['TViagViagem']['codigo_cliente'] != null
	&& isset($this->data['TViagViagem']['maximo_minutos']) && $this->data['TViagViagem']['maximo_minutos'] != null
	&& isset($this->data['TViagViagem']['data_inicial']) && $this->data['TViagViagem']['data_inicial'] != null
	&& isset($this->data['TViagViagem']['data_final']) && $this->data['TViagViagem']['data_final'] != null);
	?>
<div class='form-procurar'>
	<div class='well'>
	    <h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    	<div id='filtros'>
		    <?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'relatorio_retencao'))) ?>
		    <div class="row-fluid inline">
	            <?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', false, 'TViagViagem') ?>
		    </div>
		    <div class="row-fluid inline">
		    	<?php echo $this->Buonny->input_periodo($this) ?>
		        <?php echo $this->BForm->input('maximo_minutos', array('label' => false, 'class' => 'input-mini numeric tempo', 'placeholder' => 'Minutos', 'title' => 'Minutos Máximo no Local')) ?>
		        <?php echo $this->BForm->input('status_viagem', array('label' => false, 'class' => 'input-medium', 'options' => $status_viagem, 'empty' => 'Status Viagem')) ?>
		        <?php echo $this->BForm->input('UFOrigem', array('label' => false,'class' => 'input-mini','empty'=>'UF','title'=>'UF Origem', 'options' => $UFOrigem)) ?>
		        <?php echo $this->BForm->input('quantidade', array('label' => false,'class' => 'input-small numeric just-number','placeholder' => 'Quantidade', )) ?>
		        <?php echo $this->BForm->input('janela', array('label' => false,'class' => 'input-medium', 'options' => array(1 => 'Dentro da Janela', 2 => 'Fora da Janela'), 'empty' => 'Todos Veículos' )) ?>
		    </div>
		    <div class="row-fluid inline" id="div-tipo-alvo">
    			<?= $this->Buonny->input_alvos_bandeiras_regioes($this, array_merge($alvos_bandeiras_regioes, array('div' => '#div-tipo-alvo', 'force_model' => 'TViagViagem', 'input_codigo_cliente' => 'codigo_cliente','exibe_transportador' => false)))?>
    		</div>
		    <div class="row-fluid inline">
				<span class="label label-info">Agrupar por:</span>
	            <div id='agrupamento'>
					<?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
				</div>
			</div>
			<div class="row-fluid inline descricao_grafico" style="display:none">
				<span class="label label-info">Descrição do Gráfico:</span>
	            <div>
					<?php echo $this->BForm->input('descricao_grafico', array('type' => 'radio', 'options' => array(1 => 'Descrição', 2 => 'Código Externo'), 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
				</div>
			</div>
		    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		    <?php echo $this->BForm->end();?>
		</div>
	</div>
</div>
<?php if(!$filtrado): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php elseif(empty($dados) && empty($dados_agrupamento_veiculo)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
	<div class='well'>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
        <span class="pull-right">
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export_cd'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
        </span>
    </div>
    <div id="grafico_tempo_retencao" style="min-width: 400px; height: 400px; margin: 0 auto 10px"></div>
	<?php $series = array(
		array('type' => 'column', 'name' => "'".'Tempo Médio (minutos)'."'", 'values' => $values, 'color' => '#0088cc', 'dataLabels' => array('enabled' => true, 'formatter' => "function(){ return convertToHoursMins(this.y); }")),
		array('type' => 'spline', 'name' => "'".'Meta'."'", 'values' => $values_meta, 'color' => '#000', 'marker' => array('enabled' => 'false')),
	);
	?>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
			'title' => (!empty($limite) ? $limite.' ' : '').$titulos_grafico[$this->data['TViagViagem']['agrupamento']].' com maior Tempo de Retenção',
	        'renderTo' => 'grafico_tempo_retencao',
	        'chart' => array('type' => 'column', 'spacingBottom' => 10),
	        'yAxis' => array('title' => false),
	        'xAxis' => array('labels' => array('rotation' => -75, 'y' => 10), 'gridLineWidth' => 1),
	        'exporting' => array('buttons' => array('exportButton' => array('enabled' => 'false'), 'printButton' => array('enabled' => 'false')),),
	        'tooltip' => array(
	        	'formatter' => "'<b>'+ this.series.name +'</b><br/>'+this.x+'<br/>'+convertToHoursMins(this.y)+' horas'"
	        ),
	      	'plotOptions'=> array(
	        	'series' => array(
	        		'dataLabels' => array(
	        			'enabled' => true,
	        			'format' => "function(){ return convertToHoursMins(this.y); }"
	        		)
	        	),
	        ),
	    )));
	?>
	<?php echo $this->Javascript->codeBlock("function filtros() {return ".json_encode($this->data['TViagViagem'])."}") ?>
	<table class='table table-striped table-bordered alvos'>
		<thead>
			<tr>
				<th><?= $this->Html->link($agrupamento[$this->data['TViagViagem']['agrupamento']], 'javascript:void(0)') ?></th>
				<th class='numeric input-medium'><?= $this->Html->link('Tempo Médio', 'javascript:void(0)') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $total_tempo_total = 0 ?>
			<?php $total_quantidade_entregas = 0 ?>
			<?php foreach ($dados as $dado): ?>
				<?php if ($dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregue'] > 0): ?>
					<?php $total_tempo_total += $dado[0]['tempo_total'] ?>
					<?php $total_quantidade_entregas += $dado[0]['quantidade_entregas'] ?>
					<tr>
						<td><?= $dado[0]['descricao_agrupamento'] ?></td>
						<td class='numeric input-medium'><?= $this->Html->link(Comum::convertToHoursMins($dado[0]['tempo_medio']), 'javascript:void(0)', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), {$dado[0]['codigo_agrupamento']},['".TVlocViagemLocal::STATUS_ALVO_ENTREGANDO."','".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."'])" )) ?></td>
					</tr>
				<?php endif ?>
			<?php endforeach ?>
			<tfoot>
				<tr>
					<td>Total</td>
					<td class='numeric input-medium'><?= $this->Html->link(Comum::convertToHoursMins($total_quantidade_entregas > 0 ? ($total_tempo_total / $total_quantidade_entregas) : 0), 'javascript:void(0)', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '',['".TVlocViagemLocal::STATUS_ALVO_ENTREGANDO."','".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."'])" )) ?></td>
				</tr>
			</tfoot>
		</tbody>
	</table>
	<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
	<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php endif ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
<?php $this->addScript($this->Buonny->link_js('alvos')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$.placeholder.shim();
		setup_mascaras();
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
		jQuery("table.alvos").tablesorter();

		$("[name*=\"data[TViagViagem][agrupamento]\"]").change(function(){
			if($(this).val() == 1 || $(this).val() == 4){
				$(".descricao_grafico").show();
			}else{
				$(".descricao_grafico").hide();
			}
		});

		$("[name*=\"data[TViagViagem][agrupamento]\"]:checked").change();
    });
', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>