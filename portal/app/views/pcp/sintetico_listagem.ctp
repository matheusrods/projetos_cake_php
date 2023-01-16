<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php if(!empty($dadosSintetico)):?>
	<div id="grafico_sm" style="width:570px;height:450px;float:left"></div>
	<div id="grafico_pendentes" style="width:570px;height:450px;float:left"></div>
	<table class='table table-striped table-bordered' style="width:100%;float:left">
		<thead>
			<th><?php echo $tipoAgrupamento?></th>
			<th class="numeric">Demanda</th>
			<th class="numeric">Atendidas</th>
			<th class="numeric">Pendentes</th>
			<th class="input-mini">&nbsp</th>
			<th class="numeric">No Prazo</th>
			<th class="numeric">Possível Atraso</th>
			<th class="numeric">Atrasado</th>
		</thead>
		<tbody>
			<?php $total = 0;
				  $totalAtendidas = 0;
				  $totalNormal = 0;
				  $totalProvavelAtraso = 0;
				  $totalAtrasado = 0;
			?>
			<?php foreach ($dadosSintetico as  $dados): ?>
				<?php
				if(!empty($dados[0]['codigo_agrupamento'])){
					$codigo_selecionado = $dados[0]['codigo_agrupamento'];
				}else{
					$codigo_selecionado = '-1';	
				}
				?>
				<tr>
					<td><?= $dados[0]['tipo_agrupamento'] ?></td>
					<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($dados[0]['agrupamento_total'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', '')") ?>
					</td>
					<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($dados[0]['sm_total'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', 'S')") ?>
					</td>
					<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($dados[0]['agrupamento_total']-$dados[0]['sm_total'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', 'N')") ?>
					</td>
					<td class="input-mini">&nbsp</td>
					<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($dados[0]['status_normal'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', '1')") ?>
					</td>
					<td class='numeric input-medium'><?= $this->Html->link($this->Buonny->moeda($dados[0]['status_provavel_atraso'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', '2')") ?>
					</td>
					<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($dados[0]['status_atraso'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', '3')") ?>
					</td>
					<?php
						$total += $dados[0]['agrupamento_total'];
						$totalAtendidas += $dados[0]['sm_total'];
						$totalPendentes = $total-$totalAtendidas;
						$totalNormal += $dados[0]['status_normal'];
						$totalProvavelAtraso += $dados[0]['status_provavel_atraso'];
						$totalAtrasado += $dados[0]['status_atraso'];
					?>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td><strong>Total: </strong></td>
				<td class="numeric"><?php echo $total;?></td>
				<td class="numeric"><?php echo $totalAtendidas;?></td>
				<td class="numeric"><?php echo $totalPendentes;?></td>
				<td>&nbsp;</td>
				<td class="numeric"><?php echo $totalNormal;?></td>
				<td class="numeric"><?php echo $totalProvavelAtraso;?></td>
				<td class="numeric"><?php echo $totalAtrasado;?></td>
			</tr>
		</tfoot>
	</table>
<?php else: ?>
<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock("
	function analitico(codigo_selecionado, codigo_status) {

	var codigo_cliente = {$this->data['TIpcpInformacaoPcp']['codigo_cliente']};
	var agrupamento = {$this->data['TIpcpInformacaoPcp']['agrupamento']};

		var data_inicial = $('#TIpcpInformacaoPcpDataInicial').val();	
		var data_final = $('#TIpcpInformacaoPcpDataFinal').val();	
		var rota = $('#TIpcpInformacaoPcpRota').val();	
		var sm = $('#TIpcpInformacaoPcpSm').val();	
		var motivo = $('#TIpcpInformacaoPcpMotivo').val();	
		var tipo_veiculo = $('#TIpcpInformacaoPcpTipoVeiculoGeral').val();	
		var tipo_carga = $('#TIpcpInformacaoPcpTipoCarga').val();	
	
 		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
	    form.setAttribute('action', '/portal/pcp/analitico/1/' + Math.random());

	    field = document.createElement('input');
	    if(agrupamento == 1){
		    field.setAttribute('name', 'data[TIpcpInformacaoPcp][cd_id]');
	    }else if(agrupamento == 2){
		    field.setAttribute('name', 'data[TIpcpInformacaoPcp][bandeira_id]');
	    }else if(agrupamento == 3){
		    field.setAttribute('name', 'data[TIpcpInformacaoPcp][regiao_id]');
	    }else if(agrupamento == 4){
		    field.setAttribute('name', 'data[TIpcpInformacaoPcp][loja_id]');
	    }
	    field.setAttribute('value', codigo_selecionado);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	  	
	  	field = document.createElement('input');
	    field.setAttribute('name', 'data[TIpcpInformacaoPcp][data_inicial]');
		field.setAttribute('value', data_inicial);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[TIpcpInformacaoPcp][data_final]');
		field.setAttribute('value', data_final);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[TIpcpInformacaoPcp][rota]');
		field.setAttribute('value', rota);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[TIpcpInformacaoPcp][sm]');
		field.setAttribute('value', sm);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[TIpcpInformacaoPcp][motivo]');
		field.setAttribute('value', motivo);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[TIpcpInformacaoPcp][tipo_veiculo_geral]');
		field.setAttribute('value', tipo_veiculo);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[TIpcpInformacaoPcp][tipo_carga]');
		field.setAttribute('value', tipo_carga);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);

	    if((codigo_status >= 1) && (codigo_status <= 3)){
      		field = document.createElement('input');
		    field.setAttribute('name', 'data[TIpcpInformacaoPcp][status]');
		    field.setAttribute('value', codigo_status);
		    field.setAttribute('type', 'hidden');
		    form.appendChild(field);

		    field = document.createElement('input');
		    field.setAttribute('name', 'data[TIpcpInformacaoPcp][sm_atendida]');
		    field.setAttribute('value', '');
		    field.setAttribute('type', 'hidden');
		    form.appendChild(field);
	    }else if((codigo_status == 'S') || (codigo_status == 'N')){
		    field = document.createElement('input');
		    field.setAttribute('name', 'data[TIpcpInformacaoPcp][sm_atendida]');
		    field.setAttribute('value', codigo_status);
		    field.setAttribute('type', 'hidden');
		    form.appendChild(field);

		    field = document.createElement('input');
		    field.setAttribute('name', 'data[TIpcpInformacaoPcp][status]');
		    field.setAttribute('value', '');
		    field.setAttribute('type', 'hidden');
		    form.appendChild(field);
	    }else{
	    	field = document.createElement('input');
		    field.setAttribute('name', 'data[TIpcpInformacaoPcp][sm_atendida]');
		    field.setAttribute('value', '');
		    field.setAttribute('type', 'hidden');
		    form.appendChild(field);

		    field = document.createElement('input');
		    field.setAttribute('name', 'data[TIpcpInformacaoPcp][status]');
		    field.setAttribute('value', '');
		    field.setAttribute('type', 'hidden');
		    form.appendChild(field);
	    }


	    var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    document.body.appendChild(form);
	    form.submit();

	}"
);
if(!empty($dadosSintetico)):
	$series = array(
		array(
			'type' => 'column', 
			'name' => "'".'Demandas'."'", 
			'values' => $demandas, 
			'color' => '#1E90FF', 
			'dataLabels' => array(
				'enabled' => true, 
				'formatter' => "function(){ return (this.y); }",
				'style' => array(
					'color' => '#1E90FF', 
				),
			)
		),
		array(
			'type' => 'column', 
			'name' => "'".'Atendidos'."'", 
			'values' => $atendidos, 
			'color' => '#008000', 
			'dataLabels' => array(
				'enabled' => true, 
				'formatter' => "function(){ return (this.y); }",
				'style' => array(
					'color' => '#008000', 
				),
			)
		),
		array(
			'type' => 'column', 
			'name' => "'".'Pendentes'."'", 
			'values' => $pendentes, 
			'color' => '#FF8C00', 
			'dataLabels' => array(
				'enabled' => true, 
				'formatter' => "function(){ return (this.y); }",
				'style' => array(
					'color' => '#FF8C00', 
				),
			)
		),
		array(
			'type' => 'line', 
			'name' => "'".'Porcentagem'."'", 
			'values' => $porcentagem, 
			'color' => '#FF0000',
			'dataLabels' => array(
				'enabled' => true,
				'formatter' => "function(){return Math.floor(this.y/10)+'%';}"
			),
		 ),
	);
	echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
		'title' => "Demandas Por {$tipoAgrupamento}",
        'renderTo' => 'grafico_sm',
        'plotOptions' => array(        	
        	'series' => array('enableMouseTracking' => 'false')
        ),       
      	'chart' => array('type' => 'column', 'spacingBottom' => 70),
        'yAxis' => array('title' => false),
        'xAxis' => array('labels' => array('rotation' => -20, 'y' => 50), 'gridLineWidth' => 1),        
        'exporting' => array('buttons' => array('exportButton' => array('enabled' => 'false'), 'printButton' => array('enabled' => 'false')),),
        'legend' => array('align' => 'left', 'verticalAlign' => 'bottom', 'layout' => 'horizontal', 'floating' => 'true'),
	)));
	
	$series = array(
		array('name' => "'".'No prazo'."'",'values' => $normal,
			'color' => '#009933',
			'dataLabels' => array('enabled' => true,
				'style' => array(
					'color' => '#ffffff',
				),
				'formatter' => "function(){return (this.y);}"
			)
		),
		array('name' => "'".'Atrasado'."'", 'values' => $atraso,
			'color' => '#ff0000',
			'dataLabels' => array('enabled' => true,
				'style' => array(
					'color' => '#ffffff',
				),
				'formatter' => "function(){return (this.y);}")
		),
		array('name' => "'".'Possível atraso'."'", 'values' => $provavel_atraso,
			'color' => '#ffcc00',
			'dataLabels' => array(
				'enabled' => true,
				'style' => array(
					'color' => '#555555',
				),
				'formatter' => "function(){return (this.y);}"
			)
		),
		
	);
	echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
		'title' => 'Cargas Pendentes',
        'renderTo' => 'grafico_pendentes',        
      	'chart' => array('type' => 'bar', 'spacingBottom' => 70),
      	'yAxis' => array('title' => false),
       	'xAxis' => array('labels' => array('rotation' => -15, 'x' => -20),'gridLineWidth' => 1),
        'plotOptions' => array(        	
        	'series' => array('stacking' => 'normal')
        ),
        'exporting' => array('buttons' => array('exportButton' => array('enabled' => 'false'), 'printButton' => array('enabled' => 'false')),),
        'legend' => array('align' => 'left', 'verticalAlign' => 'bottom', 'layout' => 'horizontal', 'floating' => 'true')
    )));
endif;
?>

    