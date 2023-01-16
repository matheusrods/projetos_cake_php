<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('title' => 'Incluir Lista de Preço', 'class' => 'btn btn-success', 'escape' => false)) ?>
</div>
<table class='table table-striped'>
	<thead>
		<th><?= $this->Html->link('Código', 'javascript:void(0)') ?></th>
		<th><?= $this->Html->link('Nome', 'javascript:void(0)') ?></th>
		<th><?= $this->Html->link('Fornecedor', 'javascript:void(0)') ?></th>
		<th class='action-icon'></th>
		<th class='action-icon'></th>
		<th class='action-icon'></th>
	</thead>
	<tbody>
		<?php foreach ($listas_de_preco as $lista_de_preco): ?>
			<tr>
				<td><?= $lista_de_preco['ListaDePreco']['codigo'] ?></td>
				<td><?= $lista_de_preco['ListaDePreco']['descricao'] ?></td>
				<td><?= $lista_de_preco['Fornecedor']['nome'] ?></td>
				<td class='action-icon'><?php echo $html->link('', array('controller' => 'listas_de_preco', 'action' => 'editar', $lista_de_preco['ListaDePreco']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar Lista de Preço')); ?></td>
				<td class='action-icon'><?php echo $html->link('', array('controller' => 'listas_de_preco', 'action' => 'excluir', $lista_de_preco['ListaDePreco']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir Lista de Preço'), 'Confirma exclusão?'); ?></td>
				<td class='action-icon'><?php echo $html->link('', array('controller' => 'listas_de_preco_produto', 'action' => 'index', $lista_de_preco['ListaDePreco']['codigo']), array('class' => 'icon-wrench', 'title' => 'Editar Produtos da Lista de Preço')); ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php if (count($listas_de_preco)) $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter()")) ?>