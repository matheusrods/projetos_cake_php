<div class='well'>
	<strong>Item: </strong><?= $pite_pg_item['TPitePgItem']['pite_descricao'] ?>
</div>
<h4>Parâmetros dos Itens</h4>
<table class='table table-striped'>
	<thead>
		<th class='numeric input-mini'>Sequencia</th>
		<th>Descrição</th>
		<th class='input-mini'>Tipo</th>
		<th class='numeric input-mini'>Valor</th>
	</thead>
	<tbody>
		<?php foreach($parametros as $parametro): ?>
			<tr>
				<td class='numeric input-mini'><?= $parametro['TPaipPgAssociaItemParam']['paip_sequencia'] ?></td>
				<td><?= $parametro['TPipaPgItemParametro']['pipa_descricao'] ?></td>
				<td class='input-small'><?= $parametro['TTvalTipoValor']['tval_descricao'] ?></td>
				<td class='numeric input-mini'><?= $parametro['TPaipPgAssociaItemParam']['paip_valor'] ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>