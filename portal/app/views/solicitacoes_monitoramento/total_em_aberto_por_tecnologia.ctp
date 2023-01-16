

<div class='well'>
    <?php echo $this->BForm->create('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'total_em_aberto_por_tecnologia'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('tipo', array('class' => 'input-medium', 'options' => array(1 => 'Em aberto', 2 => 'Monitoradas'), 'label' => false)) ?>
        </div>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php if (!empty($dados)): ?>
	<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
	<br/></br>
	<div class='well'>
    	<span class="pull-right">
    		<strong>	
    			<?php echo $this->Html->link("<i class='cus-page-white-excel'></i>", array( 'controller' => $this->name, 'action' => $this->action, 'csv'), array('escape' => false, 'title' =>'Exportar Total SMs por Tecnologia'));?>
    			
			</strong>
		</span>
	</div>
	<br/>
	<table class="table table-striped table-bordered tablesorter">
		<thead>
			<tr>
				<th class="input-small"><?= $this->Html->link('Código', 'javascript:void(0)') ?></th>
				<th><?= $this->Html->link('Tecnologia', 'javascript:void(0)') ?></th>
				<th class="numeric input-small"><?= $this->Html->link(($this->data['Recebsm']['tipo'] == 1 ? 'SMs em Aberto' : 'SM Monitoradas'), 'javascript:void(0)') ?></th>
			</tr>
		</thead>
		<tbody>
		    <?php $qtd_tecnologias = 0; ?>
		    <?php $qtd_sms = 0; ?>
			<?php foreach ($dados['series'] as $serie): ?>
				<tr>
					<td><?php echo str_replace("'", "", $serie['id']) ?></td>
					<td><?php echo str_replace("'", "", $serie['name']) ?></td>
					<td class="numeric"><?= $this->Html->link($serie['values'], 'javascript:void(0)', array('onclick' => "sm_consulta_geral_por_tecnologia('{$serie['id']}', '#RecebsmTipo')")) ?></td>
				</tr>
				<?php $qtd_tecnologias++; ?>
				<?php $qtd_sms += $serie['values']; ?>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<th class="numeric"></th>
		    <th class="numeric"><?= $qtd_tecnologias ?></th>
		    <th class="numeric"><?= $qtd_sms ?></th>
		</tfoot>
	</table>
	<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter()")) ?>
	<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
	<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dados['eixo_x'], $dados['series'], array(
	    'renderTo' => 'grafico',
	    'chart' => array('type' => 'pie'),
	    'plotOptions' => array('pie' => array('showInLegend' => 'true')),
	))); ?>    
<?php endif; ?>