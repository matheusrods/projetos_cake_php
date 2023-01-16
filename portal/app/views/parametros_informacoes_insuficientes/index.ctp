<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novo Parâmetro'));?>
</div>
<table class='table table-striped'>
	<thead>
		<th class='input-medium'>Categoria</th>
		<th class='input-large'>Descrição</th>
		<th class='action-icon' colspan="1"></th>
	</thead>
	<tbody>
		<?php $i = 0 ; ?>
		<?php if (count($parametros)>0): ?>
			<?php foreach ($parametros as $parametro): ?>
				<tr>
					<td class='input-medium'><?= $categorias[$i]['ParametroInfoInsuficiente']['descricao'] ?></td>
					<td class='input-large'><?= $parametro['ParametroInfoInsuficiente']['descricao'] ?></td>
					<td class='action-icon'><?php echo $html->link('', array('controller' => 'parametros_informacoes_insuficientes', 'action' => 'editar', $parametro['ParametroInfoInsuficiente']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar paramêtro')); ?>
			         <?php echo $html->link('', array('controller' => 'parametros_informacoes_insuficientes', 'action' => 'excluir', $parametro['ParametroInfoInsuficiente']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir parâmetro'), 'Confirma exclusão?'); ?></td> 
				</tr>
				<?php $i ++ ; ?>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>