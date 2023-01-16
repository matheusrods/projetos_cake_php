<?php if (isset($viagens)): ?>
	<div class='well'>
		<strong>Data Inicial: </strong><?php echo $filtros['Recebsm']['data_inicial']; ?>
        <strong>Data Final: </strong><?php echo $filtros['Recebsm']['data_final']; ?>
        <strong>Motorista: </strong><?php echo $motorista['Motorista']['Nome']; ?>
    </div>
    
	<table class='table table-striped'>
		<thead>
			<th class='input-small'>Data</th>
			<th class='input-small numeric'>SM</th>
			<th>Placa</th>
			<th class='input-large numeric'>Máxima Direção Contínua</th>
			<th class='input-small numeric'>Total Direção</th>
			<th class='input-small numeric'>Descanso</th>
			<th></th>
			<th class='action-icon'></th>
		</thead>
		<?php $inicio = true ?>
		<?php foreach ($viagens as $viagem): ?>
			<?php foreach ($viagem[0] as $dia): ?>
				<tr>
					<td class='input-small'><?= AppModel::dbDateToDate($dia[0]['data_tecnologia']) ?></td>
					<td class='input-small numeric'><?php echo $this->Buonny->codigo_sm($viagem[1]['Recebsm']['sm']) ?></td>
					<td><?php echo $this->Buonny->placa($viagem[1]['Recebsm']['placa'], $filtros['Recebsm']['data_inicial'], $filtros['Recebsm']['data_final']) ?></td>
					<td class='input-large numeric'><?= Comum::convertToHoursMins($dia[0]['maximo_viagem']) ?></td>
					<td class='input-small numeric'><?= Comum::convertToHoursMins($dia[0]['total_direcao']) ?></td>
					<td class='input-small numeric'><?= Comum::convertToHoursMins($dia[0]['total_descanso']) ?></td>
					<td></td>
					<td class='action-icon'>
					<?php if($dia[0]['descumpriu']): ?>
						<span class="badge-empty badge badge-important" title="Limite de tempo contínuo excedido"></span>
					<?php elseif($dia[0]['total_direcao']): ?>
						<span class="badge-empty badge badge-success" title="Limite de tempo legal"></span>
					<?php else: ?>
						<span class="badge-empty badge" title="Tempo indeterminado"></span>
					<?php endif; ?>
					</td>
				</tr>
			<?php endforeach ?>
		<?php endforeach ?>
	</table>

<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<?php endif; ?>
<?php echo $this->Buonny->link_js(array('estatisticas')) ?>