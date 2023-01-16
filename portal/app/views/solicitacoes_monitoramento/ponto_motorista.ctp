<?php if (isset($sms)): ?>
	<table class='table table-striped'>
		<thead>
			<th>SM</th>
			<th>Placa</th>
		</thead>
		<?php foreach ($sms as $sm): ?>
			<tr>
				<td><?php echo $this->Buonny->codigo_sm($sm['Recebsm']['sm']) ?></td>
				<td><?php echo $this->Buonny->placa($sm['Recebsm']['placa'], $filtros['Recebsm']['data_inicial'], $filtros['Recebsm']['data_final']) ?></td>
			</tr>
		<?php endforeach ?>
	</table>
<?php endif ?>
<?php if (isset($viagens)): ?>
	<div class='well'>
		<strong>Data Inicial: </strong><?php echo $filtros['Recebsm']['data_inicial']; ?>
        <strong>Data Final: </strong><?php echo $filtros['Recebsm']['data_final']; ?>
        <strong>Motorista: </strong><?php echo $motorista['Motorista']['Nome']; ?>
    </div>
	<table class='table table-striped'>
		<thead>
			<th>SM</th>
			<th>Placa</th>
			<th>Inicio</th>
			<th>Fim</th>
			<th>Posição</th>
			<th>Macro</th>
			<th class='numeric'>Em Viagem</th>
			<th class='numeric'>Parado</th>
		</thead>
		<?php $inicio = true ?>
		<?php foreach ($viagens as $viagem): ?>
			<tr>
				<td><?php echo $this->Buonny->codigo_sm($viagem['codigo_sm']); ?>
				</td>
				<td><?php echo $viagem['placa'] ?></td>
				<td><?php echo date('d/m/Y H:i:s', $viagem['data_inicial']) ?></td>
				<td><?php echo date('d/m/Y H:i:s', $viagem['data_final']) ?></td>
				<td><?php echo $this->Buonny->posicao_geografica($viagem['descricao_sistema_final'], $viagem['latitude_final'], $viagem['longitude_final'], $viagem['placa']) ?></td>
				<td><?php echo $viagem['macro'] ?></td>
				<td class='numeric'><?php echo $viagem['status'] != 'Parado' ? $viagem['tempo'] : '' ?></td>
				<td class='numeric'><?php echo $viagem['status'] == 'Parado' ? $viagem['tempo'] : '' ?></td>
			</tr>
		<?php endforeach ?>
	</table>

<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<?php endif; ?>
<?php echo $this->Buonny->link_js(array('estatisticas')) ?>