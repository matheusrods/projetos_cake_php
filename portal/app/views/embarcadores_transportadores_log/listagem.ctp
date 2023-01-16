<div class="text-group">
<?php if(isset($preencher)):?>
    <div class="alert">
        Favor informar os dados acima.
    </div>
<?php else:?>
<?php if(isset($embarcadores_transportadores_log) && !empty($embarcadores_transportadores_log)): ?>
<table class='table table-striped'>
	<thead>
		<th>Embarcador</th>
		<th>Transportador</th>
		<th>Data Alteração</th>
		<th>Usuário</th> 
		<th>Ação no Sistema</th> 
	</thead>
	<tbody>
		<?php foreach ($embarcadores_transportadores_log as $embarcador_transportador_log): ?>
				<tr>
					<td><?= $embarcador_transportador_log['EmbarcadorTransportadorLog']['codigo_cliente_embarcador']; ?></td>
					<td><?= $embarcador_transportador_log['EmbarcadorTransportadorLog']['codigo_cliente_transportador']; ?></td>
					<td><?= $embarcador_transportador_log['EmbarcadorTransportadorLog']['data_inclusao']; ?></td>
					<td><?= $embarcador_transportador_log['Usuario']['apelido'] ?></td>
					<td><?= ($embarcador_transportador_log['EmbarcadorTransportadorLog']['acao_sistema'] == 0) ? 'INSERIDO' : (($embarcador_transportador_log['EmbarcadorTransportadorLog']['acao_sistema'] == 1) ? 'EDITADO' : 'EXCLUIDO') ?></td>
				</tr>
		<?php endforeach ?>
	</tbody>
</table>
<?php else:?>
	<div class="alert">
		Nenhum dado encontrado.
	</div>
<?php endif;?>
<?php endif;?>
</div>