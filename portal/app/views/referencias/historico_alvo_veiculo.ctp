<div class='well'>
	<strong>Data Inicial:</strong> <?= $this->data['TRefeReferencia']['data_inicial'] ?>
	<strong>Data Final:</strong> <?= $this->data['TRefeReferencia']['data_final'] ?>
	<strong>Alvo:</strong> <?= $refe_referencia['TRefeReferencia']['refe_descricao'] ?>
	<strong>Placa:</strong> <?= $veic_veiculo['TVeicVeiculo']['veic_placa'] ?>
	<strong>Transportadora:</strong> <?= $veic_veiculo['TPessPessoa']['pess_nome'] ?>
</div>
<table class='table table-striped'>
	<thead>
		<tr>
			<td>Data Entrada</td>
			<td>Data Saída</td>
			<td class='numeric'>Permanência (min)</td>
		</tr>
	</thead>
	<?php $total_veiculos = 0 ?>
	<?php foreach ($veiculos as $veiculo): ?>
		<?php $total_veiculos ++ ?>
		<tr>
			<td><?= AppModel::dbDateToDate($veiculo[0]['data_entrada']) ?></td>
			<td><?= AppModel::dbDateToDate($veiculo[0]['data_saida']) ?></td>
			<td class='numeric'><?= round($veiculo[0]['minutos_permanencia'],2) ?></td>
		</tr>
	<?php endforeach ?>
	<tfoot>
		<tr>
			<td>Total</td>
			<td></td>
			<td><?=$total_veiculos ?></td>
		</tr>
	</tfoot>
</table>