<table class='table table-striped'>
	<thead>
		<th>Hora</th>
		<th>Posição</th>
		<th>Macro</th>
	</thead>
	<?php foreach ($macros as $macro): ?>
		<tr>
			<td><?php echo $macro['TRmacRecebimentoMacro']['rmac_data_computador_bordo'] ?> </td>
			<td><?php echo $this->Buonny->posicao_geografica(substr($macro['TRposRecebimentoPosicao']['rpos_descricao_sistema'],0, 70), $macro['TRposRecebimentoPosicao']['rpos_latitude'], $macro['TRposRecebimentoPosicao']['rpos_longitude'], $placa) ?></td>
			<td><?php echo $macro['TMpadMacroPadrao']['mpad_descricao'] ?></td>
		</tr>
	<?php endforeach ?>
</table>