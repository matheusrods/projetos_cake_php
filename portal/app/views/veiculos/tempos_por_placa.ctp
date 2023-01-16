<table class='table table-striped'>
	<thead>
		<th>Inicio</th>
		<th>Fim</th>
		<th>Posição</th>
		<th class='numeric'>Em Viagem</th>
		<th class='numeric'>Parado</th>
	</thead>
	<?php $inicio = true ?>
	<?php foreach ($tempos as $tempo): ?>
		<tr>
			<td><?php echo date('d/m/Y H:i:s', $tempo['data_inicial']) ?></td>
			<td><?php echo date('d/m/Y H:i:s', $tempo['data_final']) ?></td>
			<td><?php echo $this->Buonny->posicao_geografica(substr($tempo['descricao_sistema_final'],0, 60), $tempo['latitude_final'], $tempo['longitude_final'], $tempo['placa']) ?></td>
			<td class='numeric'><?php echo $tempo['status'] != 'Parado' ? $tempo['tempo'] : '' ?></td>
			<td class='numeric'><?php echo $tempo['status'] == 'Parado' ? $tempo['tempo'] : '' ?></td>
		</tr>
	<?php endforeach ?>
</table>