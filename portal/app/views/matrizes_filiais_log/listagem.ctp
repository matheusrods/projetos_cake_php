<div class="text-group">
<?php if(isset($preencher)):?>
	<div class="alert">
		Favor informar os dados acima.
	</div>
<?php else:?>
<?php if(isset($matrizes_filiais) && !empty($matrizes_filiais)): ?>
<table class='table table-striped'>
	<thead>
		<th>Embarcador</th>
		<th>Transportador</th>
		<th>Data Alteração</th>
		<th>Usuário</th> 
		<th>Ação no Sistema</th>
	</thead>
	<tbody>
		<?php foreach ($matrizes_filiais as $matriz_filial): ?>
				<tr>
					<td><?= $matriz_filial['MatrizFilialLog']['codigo_cliente_matriz']; ?></td>
					<td><?= $matriz_filial['MatrizFilialLog']['codigo_cliente_filial']; ?></td>
					<td><?= $matriz_filial['MatrizFilialLog']['data_inclusao']; ?></td>
					<td><?= $matriz_filial['Usuario']['apelido'] ?></td>
					<td><?= ($matriz_filial['MatrizFilialLog']['acao_sistema'] == 0) ? 'INSERIDO' : (($matriz_filial['MatrizFilialLog']['acao_sistema'] == 1) ? 'EDITADO' : 'EXCLUIDO') ?></td>
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