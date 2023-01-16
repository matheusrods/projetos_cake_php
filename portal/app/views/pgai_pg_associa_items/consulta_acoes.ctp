<div class='well'>
	<strong>Item: </strong><?= $pite_pg_item['TPitePgItem']['pite_descricao'] ?>
</div>
<h4>Ações dos Itens</h4>
<table class='table table-striped'>
	<thead>
		<th class='numeric input-mini'>Código</th>
		<th class='numeric input-mini'>Sequência</th>
		<th>Descrição</th>
		<th class='numeric input-mini'>Tempo Espera</th>
		<th class='numeric input-mini'>Nº RMA</th>
		<th class='input-medium'>Data Cadastro</th>
	</thead>
	<tbody>
		<?php foreach($acoes as $acao): ?>
			<tr>
				<td class='numeric input-mini'><?= $acao['TApadAcaoPadrao']['apad_codigo'] ?></td>
				<td class='numeric input-mini'><?= $acao['TPaiaPgAssociaItemAcao']['paia_sequencia'] ?></td>
				<td><?= $acao['TApadAcaoPadrao']['apad_descricao'] ?></td>
				<td class='numeric input-mini'><?= $acao['TPaiaPgAssociaItemAcao']['paia_tempo_espera'] ?></td>
				<td class='numeric input-mini'><?= $acao['TPaiaPgAssociaItemAcao']['paia_rma'] ?></td>
				<td class='input-medium'><?= $acao['TPaiaPgAssociaItemAcao']['paia_data_cadastro'] ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>