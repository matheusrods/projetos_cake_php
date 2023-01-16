<?php echo $this->Paginator->options(array('update' => 'div#lista')); ?>

<table class='table table-striped'>
	<thead>
		<th class='input-medium'><?php echo $this->Paginator->sort('Cliente', 'codigo_cliente') ?></th>
		<th class='input-medium'><?php echo $this->Paginator->sort('Loadplan', 'loadplan') ?></th>
		<th class='input-medium'><?php echo $this->Paginator->sort('SM', 'codigo_sm') ?></th>
		<th class='input-medium'><?php echo $this->Paginator->sort('Data', 'data_inclusao') ?></th>
		<th class='input-medium'><?php echo $this->Paginator->sort('Sistema', 'sistema') ?></th>
		<th class='input-medium'><?php echo $this->Paginator->sort('Sucesso', 'sucesso') ?></th>
		<th class='input-mini'>&nbsp;</th>
	</thead>
	<tbody>
		<?php foreach ($listagem as $obj): ?>			
			<tr>
				<td><?php echo $obj['LogIntegracaoOutbox']['codigo_cliente'] ?></td>
				<td><?php echo $obj['LogIntegracaoOutbox']['loadplan'] ?></td>
				<td><?php echo $this->Buonny->codigo_sm($obj['LogIntegracaoOutbox']['codigo_sm']) ?></td>
				<td><?php echo $obj['LogIntegracaoOutbox']['data_inclusao'] ?></td>
				<td><?php echo $obj['LogIntegracaoOutbox']['sistema'] ?></td>
				<td><?php echo ($obj['LogIntegracaoOutbox']['sucesso']!='N'?'Sim':'Não') ?></td>
				<td><?php echo $this->Html->link('<i class="icon-eye-open"></i>', array('controller' => 'logs_integracoes', 'action' => 'view_outbox', $obj['LogIntegracaoOutbox']['codigo']), array('escape' => false, 'target' => '_blank')); ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="9"><strong>Total:</strong> <?php echo $this->Paginator->params['paging']['LogIntegracaoOutbox']['count']; ?></td>
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
<?php echo $this->Js->writeBuffer(); ?>
