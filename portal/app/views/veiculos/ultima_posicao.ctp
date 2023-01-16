<table class='table'>
	<thead>
		<th class='input-medium'>Data</th>
		<th>Posição</th>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $ultima_posicao['TUposUltimaPosicao']['upos_data_comp_bordo'] ?></td>
			<td><?php echo $this->Buonny->posicao_geografica($ultima_posicao['TUposUltimaPosicao']['upos_descricao_sistema'], $ultima_posicao['TUposUltimaPosicao']['upos_latitude'], $ultima_posicao['TUposUltimaPosicao']['upos_longitude'], $placa) ?></td>
		</tr>
	</tbody>
</table>