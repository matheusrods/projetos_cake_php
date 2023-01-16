<table class='table table-striped'>
	<thead>
		<tr>
			<th>Data Evento</th>
			<th>Faixa de Temperatura</th>
			<th>Temperatura</th>
			<th>Posição</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<div class="well">
			<strong>Placa: </strong> <?php echo $filtros['placa']; ?>
		</div>
		<?php if (count($historico_temperatura) > 0): ?>
			<?php foreach($historico_temperatura as $historico): ?>
				
				<tr>
					<td><?php echo AppModel::dbDateToDate($historico['TReceRecebimento']['rece_data_computador_bordo']) ?></td>
					<td><?php echo $filtros['temperatura'].'°C à '.$filtros['temperatura2'].'°C';?></td>
					<td><?php echo $historico['TRperRecebimentoPeriferico']['rper_valor'].'°C';?></td>
					<td><?php echo $this->Buonny->posicao_geografica(substr($historico['TRposRecebimentoPosicao']['rpos_descricao_sistema'],0, 60), $historico['TRposRecebimentoPosicao']['rpos_latitude'], $historico['TRposRecebimentoPosicao']['rpos_longitude'], $filtros['placa']) ?></td>
					<td><span class="badge-empty badge <?php echo ($historico['TRperRecebimentoPeriferico']['rper_valor'] < $filtros['temperatura'] || $historico['TRperRecebimentoPeriferico']['rper_valor'] > $filtros['temperatura2']) ? 'badge-important': 'badge-success' ?>" title="<?php echo ($historico['TRperRecebimentoPeriferico']['rper_valor'] < $filtros['temperatura'] || $historico['TRperRecebimentoPeriferico']['rper_valor'] > $filtros['temperatura2']) ? 'Temperatura fora da faixa': 'Temperatura dentro da faixa' ?>"></span></td>
				</tr>
				
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>