<div class='row-fluid inline'>
	<table class='table table-striped tablesorter' >
		<thead>
			<th class='input-medium'><?php echo $this->Paginator->sort('Data Inclusão','ConfigComissaoCorreLog.data_inclusao') ?></th>
			<th class='input-large'><?php echo $this->Paginator->sort('Data Alteração','ConfigComissaoCorreLog.data_alteracao') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Usuario Inclusão','ConfigComissaoCorreLog.codigo_usuario_inclusao') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Usuario Alterou','ConfigComissaoCorreLog.codigo_usuario_alteracao') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Ação', 'ConfigComissaoCorreLog.acao_sistema') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Corretora','Corretora.nome') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Produto','Produto.descricao') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Serviço','Servico.descricao') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('De (R$) ','ConfigComissaoCorreLog.preco_de') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Até (R$)','ConfigComissaoCorreLog.preco_ate') ?></th>
			<th class='input-small'><?php echo $this->Paginator->sort('Impostos','ConfigComissaoCorreLog.percentual_impostos')?></th>	
			<th class='input-small'><?php echo $this->Paginator->sort('Comissão','ConfigComissaoCorreLog.percentual_comissao') ?></th>	
		</thead>
		<tbody>	
			<?php foreach ($listagem as $obj):?>
				<tr>
					<td><?php echo $obj['ConfigComissaoCorreLog']['data_inclusao']?></td>
					<td><?php echo $obj['ConfigComissaoCorreLog']['data_alteracao']?></td>
					<td><?php echo $obj['UsuarioInclusao']['nome']?></td>
					<td><?php echo $obj['UsuarioAlterou']['nome']?></td>
					<td><?php echo $obj[0]['acao']?></td>
					<td><?php echo $obj['Corretora']['nome']?></td>
					<td><?php echo $obj['Produto']['descricao']?></td>
					<td><?php echo $obj['Servico']['descricao']?></td>
					<td class='numeric'><?php echo $this->Buonny->moeda($obj['ConfigComissaoCorreLog']['preco_de'], array('nozero' => true)) ?></td>
					<td class='numeric'><?php echo $this->Buonny->moeda($obj['ConfigComissaoCorreLog']['preco_ate'], array('nozero' => true)) ?></td>
					<td class='numeric'><?php echo $this->Buonny->moeda($obj['ConfigComissaoCorreLog']['percentual_impostos'], array('nozero' => true)) ?></td>
					<td class='numeric'><?php echo $this->Buonny->moeda($obj['ConfigComissaoCorreLog']['percentual_comissao'],array('nozero' => true)) ?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan = "13"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ConfigComissaoCorreLog']['count']; ?></td>
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
	<div class='row-fluid'>
  		<?php echo $this->Html->link('Voltar',array('controller' => 'configuracao_comissoes','action' => 'por_corretora'), array('class' => 'btn')); ?>
	</div>	
</div>
<?php echo $this->Js->writeBuffer();?>

<?php echo $this->Buonny->link_css('tablesorter') ?>
<?php echo $this->Buonny->link_js('jquery.tablesorter.min') ?>
<?php echo $this->Javascript->codeBlock("jQuery('table.table').tablesorter()") ?>

