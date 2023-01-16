<div class='form-procurar well'>
	<?php echo $this->BForm->create('Cliente', array('action' => 'estatisticas_buonny_credit')); ?>
		<?php echo $this->BForm->input('Ano', array('label' => false, 'class' => 'input-small', 'options' => array(date('Y') => date('Y'), '2011'=>'2011', '2012'=>'2012'))); ?>
		<?php echo $this->BForm->submit('Enviar'); ?>
	<?php echo $this->BForm->end(); ?>
</div>
<div class="row-fluid">
	<div class='lista'>
	<?php if(isset($dados)){ ?>
		<div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
		<table class="table table-striped table-bordered tablesorter">
			<thead>
				<tr>
					<th><?= $this->Html->link('Mês', 'javascript:void(0)') ?></th>
					<th><?= $this->Html->link('Serviço', 'javascript:void(0)') ?></th>
					<th class='numeric'><?= $this->Html->link('Número de Consultas', 'javascript:void(0)') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($dados as $dado): ?>
				<tr>
					<td><?= $dado['0']['ano_mes']?></td>
					<td><?= $dado['0']['nome_servico']?></td>
					<td class='numeric'><?= $dado['0']['numero_consultas']?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
		<?php
			$this->addScript($this->Buonny->link_css('tablesorter'));
			$this->addScript($this->Buonny->link_js('jquery.tablesorter.min'));
			$this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter({sortList: [[0,1]],})"));
			$this->addScript($this->Buonny->link_js('highcharts/highcharts'));
			$this->addScript($this->Buonny->link_js('highcharts/modules/exporting'));
			echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
			    'renderTo' => 'grafico',
				'chart' => array('type' => 'line'),
				'yAxis' => array('title' => ''),
				'xAxis' => array('labels' => array('rotation' => -75, 'y' => 40), 'gridLineWidth' => 1),				    
			)));
		}
		?>
	</div>
</div>