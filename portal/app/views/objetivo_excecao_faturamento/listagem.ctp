<?php if(isset($listagem) && !empty($listagem)):?>
	<?php
	    echo $paginator->options(array('update' => 'div.lista'));
	?>
	<table class='table table-striped table-bordered' style="max-width:none; white-space:nowrap">
		<thead>
			<th>Cliente</th>
			<th>Produto</th>
			<th class="numeric input-small">Faturamento Médio</th>
			<th class="numeric input-mini">Mês</th>
			<th class="numeric input-mini">Ano</th>
			<th>&nbsp;</th>
		</thead>
		<tbody>	
			<?php foreach ($listagem as $dado): ?>
				<tr>			
					<td><?= $dado['ObjetivoExcecaoFaturamento']['codigo_cliente'] .' - '.$dado['Cliente']['razao_social'] ?></td>
					<td><?= $dado['Produto']['descricao'] ?></td>
					<td class="numeric"><?= $this->Buonny->moeda($dado['ObjetivoExcecaoFaturamento']['faturamento_medio']) ?></td>
					<td class="numeric"><?= $dado['ObjetivoExcecaoFaturamento']['mes'] ?></td>
					<td class="numeric"><?= $dado['ObjetivoExcecaoFaturamento']['ano'] ?></td>
					<td class="numeric">
						<?= $this->Html->link('', array('action' => 'editar', $dado['ObjetivoExcecaoFaturamento']['codigo'], rand()), array('title' => 'Editar', 'class' => 'icon-edit')) ?>
						<?= $html->link('', array('controller' => $this->name,'action' => 'excluir', $dado['ObjetivoExcecaoFaturamento']['codigo'], rand()), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Tem certeza que deseja excluir?'); ?>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>	
	</table>
	<div class='row-fluid'>
	    <div class='numbers span6'>
	    	<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	        <?php echo $this->Paginator->numbers(); ?>
	    	<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	    </div>
	    <div class='counter span6'>
	        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages% - Total de %count%')); ?>
	    </div>
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
	<div class="alert">Nenhum registro encontrado</div>
<?php endif;?>