<?php echo $paginator->options(array('update' => 'div#lista')); ?>

<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir', array('controller' => 'configuracao_comissoes','action' => 'incluir_por_corretora'), array('class' => 'btn btn-success','onclick' => 'return open_dialog(this, "Incluir Configuração por Corretora", 580)', 'title' => 'Incluir Configuração por Corretora' )); ?>
</div>

<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class='input-xlarge'><?php echo $this->Paginator->sort('Corretora', 'Corretora.nome') ?></th>
			<th class='input-large'><?php echo $this->Paginator->sort('Produto', 'Produto.descricao') ?></th>
			<th class='input-large'><?php echo $this->Paginator->sort('Serviço', 'Servico.descricao') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Verificar Preço Unitário', 'verificar_preco_unitario') ?></th>
			<th class='input-mini numeric'><?php echo $this->Paginator->sort('De (R$)', 'preco_de') ?></th>
			<th class='input-mini numeric'><?php echo $this->Paginator->sort('Até (R$)', 'preco_ate') ?></th>
			<th class='input-mini numeric'><?php echo $this->Paginator->sort('Impostos', 'percentual_impostos') ?></th>
			<th class='input-mini numeric'><?php echo $this->Paginator->sort('Comissão', 'percentual_comissao') ?></th>
			<th class="action-icon"></th>
			<th class="action-icon"></th>
			<th class="action-icon"></th>
		</thead>
		
		<tbody>				
			<?php foreach ($listagem as $obj):?>
				<tr>
					<td><?php echo $obj['Corretora']['nome']?></td>
					<td><?php echo $obj['Produto']['descricao']?></td>
					<td><?php echo $obj['Servico']['descricao']?></td>
					<td><?php echo ($obj['ConfiguracaoComissaoCorre']['verificar_preco_unitario']?"Sim":"Não") ?></td>
					<td class='numeric'><?php echo $this->Buonny->moeda($obj['ConfiguracaoComissaoCorre']['preco_de'], array('nozero' => true)) ?></td>
					<td class='numeric'><?php echo $this->Buonny->moeda($obj['ConfiguracaoComissaoCorre']['preco_ate'], array('nozero' => true)) ?></td>
					<td class='numeric'><?php echo $obj['ConfiguracaoComissaoCorre']['percentual_impostos']?> %</td>
					<td class='numeric'><?php echo $obj['ConfiguracaoComissaoCorre']['percentual_comissao']?> %</td>
					<td><?php echo $html->link('', array('controller' => 'configuracao_comissoes', 'action' => 'atualizar_por_corretora', $obj['ConfiguracaoComissaoCorre']['codigo']), array('onclick' => 'return open_dialog(this, "Atualizar Comissão", 560)','class' => 'icon-edit dialog', 'title' => 'Atualizar comissão')) ?></td>
					<td><?php echo $html->link('', array('controller' => 'configuracao_comissoes', 'action' => 'historico_config_comissao_corre', $obj['ConfiguracaoComissaoCorre']['codigo']), array('class' => 'icon-eye-open', 'title' => 'Histórico comissão')) ?></td>
					<td><?php echo $html->link('', array('controller' => 'configuracao_comissoes', 'action' => 'excluir_por_corretora', $obj['ConfiguracaoComissaoCorre']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir comissão'), 'Confirma exclusão?'); ?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan = "14"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ConfiguracaoComissaoCorre']['count']; ?></td>
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