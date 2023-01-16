<?php echo $paginator->options(array('update' => 'div#lista')); ?>

<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('controller' => 'configuracao_comissoes','action' => 'incluir'), array('class' => 'btn btn-success', 'escape' => false )); ?>
</div>

<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-medium'><?php echo $this->Paginator->sort('Filial', 'EnderecoRegiao.descricao') ?></th>
			<th class='input-xlarge'><?php echo $this->Paginator->sort('Produto', 'NProduto.descricao') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Tipo', 'regiao_tipo_faturamento') ?></th>
			<th class='input-mini numeric'><?php echo $this->Paginator->sort('Comissão', 'percentual') ?></th>
			<th class="action-icon"></th>
			<th class="action-icon"></th>
		</thead>
		
		<tbody>				
			<?php foreach ($listagem as $obj):?>
				<tr>
					<td>
						<?php echo $obj['EnderecoRegiao']['descricao']?>
					</td>
					<td>
						<?php echo $obj['NProduto']['descricao']?>
					</td>
					<td>
						<?php echo $obj['ConfiguracaoComissao']['regiao_tipo_faturamento'] == 0 ? 'Parcial' : 'Total' ?>
					</td>
					<td class='numeric'>
						<?php echo $obj['ConfiguracaoComissao']['percentual']?> %
					</td>
										
					<td class='action-icon'>
						<?php echo $html->link('', array('controller' => 'configuracao_comissoes', 'action' => 'atualizar', $obj['ConfiguracaoComissao']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'Atualizar comissão')) ?>
					</td>
					<td>
						<?php echo $html->link('', array('controller' => 'configuracao_comissoes', 'action' => 'excluir', $obj['ConfiguracaoComissao']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir comissão'), 'Confirma exclusão?'); ?>
					</td> 
				

				</tr>
			<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan = "8"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ConfiguracaoComissao']['count']; ?></td>
			</tr>
		</tfoot>
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
</div>	
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Buonny->link_js('estatisticas') ?>
	