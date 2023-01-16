<?php if(isset($this->passedArgs[0]) && $this->passedArgs[0] == 'export_veiculo'){

	header('Content-type: application/vnd.ms-excel'); 
	header(sprintf('Content-Disposition: attachment; filename="%s"', basename('alvos_parados_em_alvos.csv')));
	header('Pragma: no-cache');
	
	echo iconv('UTF-8', 'ISO-8859-1', '"Tipo Veículo";"SMs";"Alvos Acima do Tempo (Entregando)";"Alvos Acima do Tempo (Entregue)";"Total alvos (Entregando)";"Total alvos (Entregue)";"Tempo Médio"')."\n";

		$total_sm = 0;
		$total_maximo_entregando = 0;
		$total_maximo_entregue = 0;
		$total_tempo_total = 0;
		$total_entregando = 0;
		$total_entregue = 0;
		$total_quantidade_entregas = 0;
		foreach ($dados_agrupamento_veiculo as $dado){
			if ($dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregue'] > 0){
				$total_sm += $dado[0]['qtd_sm'];
				$total_maximo_entregando += $dado[0]['qtd_acima_maximo_entregando'];
				$total_maximo_entregue += $dado[0]['qtd_acima_maximo_entregue'];
				$total_entregando += $dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregando'];
				$total_entregue += $dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregue'];
				$tempo_medio = ($dado[0]['quantidade_entregas'] > 0 ? $dado[0]['tempo_total'] / $dado[0]['quantidade_entregas'] : $dado[0]['quantidade_entregas']);
				$total_tempo_total += $dado[0]['tempo_total'];
				$total_quantidade_entregas += $dado[0]['quantidade_entregas'];
				
				$linha  = '"'.$dado[0]['descricao_agrupamento'].'";';
		    	$linha .= '"'.$dado[0]['qtd_sm'].'";';
		    	$linha .= '"'.$this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregando']).'";';
				$linha .= '"'.$this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregue']).'";';
				$linha .= '"'.$this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregando']).'";';
				$linha .= '"'.$this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregue']).'";';
				$linha .= '"'.Comum::convertToHoursMins($tempo_medio).'";';
				$linha .= "\n";
			    echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
	 			}
	 		}
	 		die;
}
?>

<div class="well">
		<span class="pull-right">
			<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export_veiculo'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
		</span>
</div>
<table class='table table-striped table-bordered alvos-veiculos'>
	<thead>
		<tr>
			<th colspan=2></th>
			<th colspan=2 style='text-align:center'>Alvos Acima do Tempo</th>
			<th colspan=2 style='text-align:center'>Total Alvos</th>
			<th></th>
		</tr>
		<tr>
			<th><?= $this->Html->link('Tipo Veículo', 'javascript:void(0)') ?></th>
			<th class='numeric input-small'><?= $this->Html->link('SMs', 'javascript:void(0)') ?></th>
			<th class='numeric input-small'><?= $this->Html->link('Entregando', 'javascript:void(0)') ?></th>
			<th class='numeric input-small'><?= $this->Html->link('Entregue', 'javascript:void(0)') ?></th>
			<th class='numeric input-small'><?= $this->Html->link('Entregando', 'javascript:void(0)') ?></th>
			<th class='numeric input-small'><?= $this->Html->link('Entregue', 'javascript:void(0)') ?></th>
			<th class='numeric input-small'><?= $this->Html->link('Tempo Médio', 'javascript:void(0)') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $total_sm = 0 ?>
		<?php $total_maximo_entregando = 0 ?>
		<?php $total_maximo_entregue = 0 ?>
		<?php $total_tempo_total = 0 ?>
		<?php $total_entregando = 0 ?>
		<?php $total_entregue = 0 ?>
		<?php $total_quantidade_entregas = 0 ?>
		<?php foreach ($dados_agrupamento_veiculo as $dado): ?>
			<?php if ($dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregue'] > 0): ?>
				<?php $total_sm += $dado[0]['qtd_sm'] ?>
				<?php $total_maximo_entregando += $dado[0]['qtd_acima_maximo_entregando'] ?>
				<?php $total_maximo_entregue += $dado[0]['qtd_acima_maximo_entregue'] ?>
				<?php $total_entregando += $dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregando'] ?>
				<?php $total_entregue += $dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregue'] ?>
				<?php $tempo_medio = ($dado[0]['quantidade_entregas'] > 0 ? $dado[0]['tempo_total'] / $dado[0]['quantidade_entregas'] : $dado[0]['quantidade_entregas']) ?>
				<?php $total_tempo_total += $dado[0]['tempo_total'] ?>
				<?php $total_quantidade_entregas += $dado[0]['quantidade_entregas'] ?>
				<tr>
					<td><?= $dado[0]['descricao_agrupamento'] ?></td>
					<td class='numeric input-small'><?= $dado[0]['qtd_sm'] ?></td>
					<td class='numeric input-small'><?= $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregando'], array('nozero' => true, 'places' => 0)) ?></td>
					<td class='numeric input-small'><?= $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregue'], array('nozero' => true, 'places' => 0)) ?></td>
					<td class='numeric input-small'><?= $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregando'], array('nozero' => true, 'places' => 0)) ?></td>
					<td class='numeric input-small'><?= $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregue'], array('nozero' => true, 'places' => 0)) ?></td>
					<td class='numeric input-small'><?= Comum::convertToHoursMins($tempo_medio) ?></td>
				</tr>
			<?php endif ?>
		<?php endforeach ?>
		<tfoot>
			<tr>
				<td>Total</td>
				<td class='numeric input-small'><?= $total_sm ?></td>
				<td class='numeric input-small'><?= $total_maximo_entregando ?></td>
				<td class='numeric input-small'><?= $total_maximo_entregue ?></td>
				<td class='numeric input-small'><?= $total_entregando ?></td>
				<td class='numeric input-small'><?= $total_entregue ?></td>
				<td class='numeric input-small'><?= Comum::convertToHoursMins($total_quantidade_entregas > 0 ? ($total_tempo_total / $total_quantidade_entregas) : 0) ?></td>
			</tr>
		</tfoot>
	</tbody>
</table>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		jQuery("table.alvos-veiculos").tablesorter()
    });', false);
?>