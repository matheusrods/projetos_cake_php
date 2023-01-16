<div class="text-group">
<?php if(isset($preencher)):?>
	<div class="alert">
		Favor informar os dados acima.
	</div>
<?php else:?>
<?php if(isset($matrizes_produtos_pagadores) && !empty($matrizes_produtos_pagadores)): ?>
<table class='table table-striped'>
	<thead>
		<th>Cliente Pagador</th>
		<th>Produto</th>
		<th>Data Alteração</th>
		<th>Usuário</th> 
		<th>Ação no Sistema</th>
	</thead>
	<tbody>
		<?php foreach ($matrizes_produtos_pagadores as $matriz_prduto_pagador): ?>
				<tr>
					<td><?= $matriz_prduto_pagador['MatrizProdutoPagadorLog']['codigo_cliente_pagador']; ?></td>
					<td><?= $matriz_prduto_pagador['Produto']['descricao']; ?></td>
					<td><?= $matriz_prduto_pagador['MatrizProdutoPagadorLog']['data_inclusao']; ?></td>
					<td><?= $matriz_prduto_pagador['Usuario']['apelido'] ?></td>
					<td><?= ($matriz_prduto_pagador['MatrizProdutoPagadorLog']['acao_sistema'] == 0) ? 'INSERIDO' : (($matriz_prduto_pagador['MatrizProdutoPagadorLog']['acao_sistema'] == 1) ? 'EDITADO' : 'EXCLUIDO') ?></td>
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