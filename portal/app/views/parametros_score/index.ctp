<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Parametros'));?>
</div>
<table class='table table-striped'>
	<thead>
		<th class='input-large'>Nivel</th>
		<th class='numeric input-large'>% de Pontos</th>
		<th class='numeric input-large'> Valor R$</th>
		<th class='action-icon' colspan="1"></th>
	</thead>
	<tbody>
		<?php if (count($parametros)>0): ?>
			<?php foreach ($parametros as $parametros): ?>
				<tr>
					<td class='input-small'><?= $parametros['ParametroScore']['nivel'] ?></td>
					<td class='numeric input-small',maxlength ='false'><?= $parametros['ParametroScore']['pontos'] ?></td>
					<td class='numeric moeda input-large',maxlength ='false'><?= $this->Buonny->moeda($parametros['ParametroScore']['valor'])?></td>
					<td class='action-icon'><?php echo $html->link('', array('controller' => 'parametros_score', 'action' => 'editar', $parametros['ParametroScore']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar paramêtro')); ?>
			         <?php echo $html->link('', array('controller' => 'parametros_score', 'action' => 'excluir', $parametros['ParametroScore']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir parâmetro'), 'Confirma exclusão?'); ?></td> 
				</tr>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>