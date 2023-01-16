<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novas Exceções'));?>
</div>
<table class='table table-striped'>
	<thead>
		<th class='input-small'>Código</th>
		<th>Cliente</th>
		<th>Produto</th>
		<th class='numeric input-small'>Valor Acima</th>
		<th class='numeric input-small'>IRRF %</th>
		<th class='numeric input-small'>Valor Acima</th>
		<th class='numeric input-small'>Fórmula ISS/PCC</th>
		<th class='action-icon'></th>
		<th class='action-icon'></th>
	</thead>
	<tbody>
		<?php if (count($excecoes)>0): ?>
			<?php foreach ($excecoes as $excecao): ?>
				<tr>
					<td class='input-small'><?= $excecao['ExcecaoFormula']['codigo_cliente_pagador'] ?></td>
					<td><?= $excecao['Cliente']['razao_social'] ?></td>
					<td><?= $excecao['Produto']['descricao'] ?></td>
					<td class='numeric input-small'><?= $excecao['ExcecaoFormula']['valor_acima_irrf'] ?></td>
					<td class='numeric input-small'><?= $excecao['ExcecaoFormula']['percentual_irrf'] ?></td>
					<td class='numeric input-small'><?= $excecao['ExcecaoFormula']['valor_acima_formula'] ?></td>
					<td class='numeric input-small'><?= $excecao['ExcecaoFormula']['codigo_formula_naveg'] ?></td>
					<td class='action-icon'><?php echo $html->link('', array('controller' => 'excecoes_formulas', 'action' => 'editar', $excecao['ExcecaoFormula']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar exceção')); ?></td>
			        <td class='action-icon'><?php echo $html->link('', array('controller' => 'excecoes_formulas', 'action' => 'excluir', $excecao['ExcecaoFormula']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir exceção'), 'Confirma exclusão?'); ?></td>
				</tr>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>