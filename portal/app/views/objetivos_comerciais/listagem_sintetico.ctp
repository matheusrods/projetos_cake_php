<?php if(isset($listagem) && !empty($listagem)):?>
	<div class="row-fluid inline">
    	<?= $this->element('objetivo_comercial/grafico') ?>
	</div>
	<table class='table table-striped table-bordered' style='max-width:none;white-space:nowrap'>
		<thead>
			<th>&nbsp;</th>
			<th style="text-align:center" colspan="3">Visitas</th>
			<th style="text-align:center" colspan="3">Faturamento</th>
			<th style="text-align:center" colspan="3">Novos Clientes</th>
		</thead>
		<thead>
			<th><?= $agrupamentoDescricao;?></th>
			<th class="numeric input-small">Objetivo</th>
			<th class="numeric input-small">Realizado</th>
			<th class="numeric input-small">%</th>
			<th class="numeric input-small">Objetivo</th>
			<th class="numeric input-small">Realizado</th>
			<th class="numeric input-small">%</th>
			<th class="numeric input-small">Objetivo</th>
			<th class="numeric input-small">Realizado</th>
			<th class="numeric input-small">%</th>
		</thead>
		<tbody>
			<?php
				$totalVisitasRealizado = 0;
				$totalClientesRealizado = 0;
				$totalFaturamentoRealizado = 0;
				$totalVisitasObjetivo = 0;
				$totalClientesObjetivo = 0;
				$totalFaturamentoObjetivo = 0;											
			?>
			
			<?php foreach ($listagem as $dado): ?>
				<tr>
					<td><?= $dado[0]['descricao']?></td>
					<td class="numeric input-small"><?= $this->Buonny->moeda($dado[0]['visitas_objetivo'], array('nozero' => true, 'places' => 0)) ?></td>
					<td class="numeric input-small"><?= $this->Html->link($this->Buonny->moeda($dado[0]['visitas_realizadas'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$agrupamento_atual}',{$dado[0]['codigo_descricao']},1)") ?></td>
					<td class="numeric input-small"><?= ($dado[0]['visitas_objetivo'] > 0) ? $this->Buonny->moeda((($dado[0]['visitas_realizadas'] / $dado[0]['visitas_objetivo']) * 100),array('nozero' => true,'places' => 2)) : ''?></td>
					<td class="numeric input-small"><?= $this->Buonny->moeda($dado[0]['faturamento_objetivo'], array('nozero' => true,))?></td>
					<td class="numeric input-small"><?= $this->Html->link($this->Buonny->moeda($dado[0]['faturamento_realizado'],array('nozero' => true,)), "javascript:analitico('{$agrupamento_atual}',{$dado[0]['codigo_descricao']},3)") ?></td>
					<td class="numeric input-small"><?= ($dado[0]['faturamento_objetivo'] > 0) ? $this->Buonny->moeda((($dado[0]['faturamento_realizado'] / $dado[0]['faturamento_objetivo']) * 100),array('nozero' => true,'places' => 2))  : ''?></td>
					<td class="numeric input-small"><?= $this->Buonny->moeda($dado[0]['novos_clientes_objetivo'], array('nozero' => true, 'places' => 0)) ?></td>
					<td class="numeric input-small"><?= $this->Html->link($this->Buonny->moeda($dado[0]['cliente_novo'], array('nozero' => true,'places' => 0)), "javascript:analitico('{$agrupamento_atual}',{$dado[0]['codigo_descricao']},2)") ?></td>
					<td class="numeric input-small"><?= ($dado[0]['novos_clientes_objetivo'] > 0) ? $this->Buonny->moeda((($dado[0]['cliente_novo'] / $dado[0]['novos_clientes_objetivo']) * 100),array('nozero' => true,'places' => 2)) : ''?></td>
				</tr>				
				<?php
					$totalVisitasObjetivo += $dado[0]['visitas_objetivo'];
					$totalVisitasRealizado += $dado[0]['visitas_realizadas'];
					$totalFaturamentoObjetivo += $dado[0]['faturamento_objetivo'];						
					$totalFaturamentoRealizado += $dado[0]['faturamento_realizado'];
					$totalClientesObjetivo += $dado[0]['novos_clientes_objetivo'];
					$totalClientesRealizado += $dado[0]['cliente_novo'];
				?>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td><strong>Total: </strong></td>
				<td class="numeric"><?= ($totalVisitasObjetivo > 0) ? $totalVisitasObjetivo : '';?></td>
				<td class="numeric"><?= ($totalVisitasRealizado > 0) ? $totalVisitasRealizado : '';?></td>
				<td class="numeric"><?= ($totalVisitasObjetivo > 0) ? $this->Buonny->moeda((($totalVisitasRealizado / $totalVisitasObjetivo)*100),array('nozero' => true,'places' => 2)) : ''?></td>
				<td class="numeric"><?= ($totalFaturamentoObjetivo > 0) ? $this->Buonny->moeda($totalFaturamentoObjetivo) : '';?></td>
				<td class="numeric"><?= ($totalFaturamentoRealizado > 0) ? $this->Buonny->moeda($totalFaturamentoRealizado) : '';?></td>
				<td class="numeric"><?= ($totalFaturamentoObjetivo > 0) ? $this->Buonny->moeda((($totalFaturamentoRealizado / $totalFaturamentoObjetivo)*100),array('nozero' => true,'places' => 2)) : ''?></td>				
				<td class="numeric"><?= ($totalClientesObjetivo > 0) ? $totalClientesObjetivo : '';?></td>
				<td class="numeric"><?= ($totalClientesRealizado > 0) ? $totalClientesRealizado : '';?></td>
				<td class="numeric"><?= ($totalClientesObjetivo > 0) ? $this->Buonny->moeda((($totalClientesRealizado / $totalClientesObjetivo)*100),array('nozero' => true,'places' => 2)) : ''?></td>

			</tr>
		</tfoot>
</table>	
<?php echo $this->Javascript->codeBlock("
	function analitico(agrupamento,codigo_selecionado,visualizacao) {	
	
 		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
	    form.setAttribute('action', '/portal/objetivos_comerciais/analitico/1/' + Math.random());

	    field = document.createElement('input');
	   	
	   	if(agrupamento == 1){
			field.setAttribute('name', 'data[ObjetivoComercial][codigo_endereco_regiao]');
	   	}else if(agrupamento == 2){
			field.setAttribute('name', 'data[ObjetivoComercial][codigo_produto]');
	   	}else if(agrupamento == 3){
			field.setAttribute('name', 'data[ObjetivoComercial][codigo_gestor]');
	   	}else if(agrupamento == 4){
			field.setAttribute('name', 'data[ObjetivoComercial][codigo_diretoria]');
	   	}
	    field.setAttribute('value', codigo_selecionado);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field); 

	   	field = document.createElement('input');
	   	
	   	field.setAttribute('name', 'data[ObjetivoComercial][visualizacao]');
	   	field.setAttribute('value', visualizacao);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field); 
	    

	    var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    document.body.appendChild(form);
	    form.submit();

	}"
);
?>
<?php else:?>
	<div class="alert">Nenhum registro encontrado</div>	
<?php endif;?>