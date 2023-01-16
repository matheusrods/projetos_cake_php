<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir_excecao'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar'));?>
</div>
<br/>
<?php if(isset($listagem) && !empty($listagem)):?>
	<?php
	    echo $paginator->options(array('update' => 'div.lista'));
	?>
	<table class='table table-striped' style='max-width:none;white-space:nowrap'>
		<thead>
			<th>Código do Cliente</th>
			<th>Cliente</th>
			<th>Produto</th>
			<th colspan="2">&nbsp;</th>
		</thead>
		<tbody>
			<?php foreach ($listagem as $dado): ?>
				<tr style='word-wrap:none'>
					<td><?= $dado['ObjetivoComercialExcecao']['codigo_cliente'] ?></td>
					<td><?= $dado['Cliente']['razao_social'] ?></td>
					<td><?= $dado['Produto']['descricao'] ?></td>					
					<td class="numeric"><?php echo $html->link('', array('controller' => 'objetivos_comerciais', 'action' => 'editar_excecao', $dado['ObjetivoComercialExcecao']['codigo_pai']), array('class' => 'icon-edit', 'title' => 'Editar')); ?></td>
					<td><?php echo $html->link('', array('controller' => 'objetivos_comerciais', 'action' => 'excluir_excecao', $dado['ObjetivoComercialExcecao']['codigo_pai']), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Confirma exclusão?'); ?></td>
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
	        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	    </div>
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
	<div class="alert">Nenhum registro encontrado</div>	
<?php endif;?>