<table class='table table-striped'>
	<thead>
		<th>Data/Hora</th>
		<th>Tipo alvo</th>
		<th>Posição</th>
		<th>Evento</th>
	</thead>				
	<tbody>
		<?php foreach($eventos_logisticos as $key => $value): ?>
			<tr>
				<td><?php echo AppModel::dbDateToDate(substr($value[0]['esis_data_cadastro'],0,16)); ?></td>
				<td><?php echo $value[0]['cref_descricao']; ?></td>
				<td><a href="#" onclick="mapa_coordenadas('<?php echo $value[0]['refe_latitude'] ?>', '<?php echo $value[0]['refe_longitude'] ?>', '<?php echo $value[0]['veic_placa'] ?>')"><?php echo $value[0]['refe_descricao']; ?></a></td>
				<td><?php echo $value[0]['espa_descricao']; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>