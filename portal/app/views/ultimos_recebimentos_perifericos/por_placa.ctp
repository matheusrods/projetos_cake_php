<table class='table table'>
	<thead>
		<th>Periferico</th>
		<th>Evento</th>
		<th>Valor</th>
		<th>Data</th>
	</thead>
	<tbody>
		<?php if($dados): ?>
			<?php foreach ($dados as $value): ?>
				<tr>
					<td><?= $value['TPpadPerifericoPadrao']['ppad_descricao'] ?></td>
					<td><?= $value['TEppaEventoPerifericoPadrao']['eppa_descricao'] ?></td>
					<td><?= ($value['TVepeValorEventoPeriferico']['vepe_descricao'] ? $value['TVepeValorEventoPeriferico']['vepe_descricao'] : $value['TUrpeUltimoRecPeriferico']['urpe_valor']) ?></td>
					<td><?= $value['TUrpeUltimoRecPeriferico']['urpe_data_cadastro'] ?></td>
				</tr>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>