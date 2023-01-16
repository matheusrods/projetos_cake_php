<?php if (count($dados)): ?>
	<div class="well">
		<span class="pull-right">
            <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>   
        </span>
	</div>
	<?php 
	    echo $paginator->options(array('update' => 'div.comissoes_analitico_listagem')); 
	?>
	<table class='table table-striped' style='max-width:none;white-space:nowrap'>
		<thead>
			<th>Código</th>
			<th>Cliente</th>
			<th>Documento</th>
			<th class='numeric'>Valor</th>
			<th class='numeric'>%</th>
			<th class='numeric'>Comissão</th>
			<th>Tipo Faturamento</th>
			<th>Produto</th>
			<th>Filial</th>
			<th>Gestor</th>
			<th>Corretora</th>
			<th>Seguradora</th>
		</thead>
		<tbody>
			<?php foreach ($dados as $dado): ?>
				<tr style='word-wrap:none'>
					<td><?= $dado[0]['cliente_codigo'] ?></td>
					<td><?= $dado[0]['cliente_nome'] ?></td>
					<td><?= $dado[0]['numero'] ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($dado[0]['vlmerc'], array('nozero' => true)) ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($dado[0]['percentual'], array('nozero' => true)) ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($dado[0]['percentual'] / 100 * $dado[0]['vlmerc'], array('nozero' => true)) ?></td>
					<td><?= ($dado[0]['tipo_faturamento'] == 1 ? 'Total' : 'Parcial') ?></td>
					<td><?= $dado[0]['produto_nome'] ?></td>
					<td><?= $dado[0]['filial_nome'] ?></td>
					<td><?= $dado[0]['gestor_nome'] ?></td>
					<td><?= $dado[0]['corretora_nome'] ?></td>
					<td><?= $dado[0]['seguradora_nome'] ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan='3'></td>
				<td class='numeric'><?= $this->Buonny->moeda($totais[0][0]['valor'], array('nozero' => true)) ?></td>
				<td></td>
				<td class='numeric'><?= $this->Buonny->moeda($totais[0][0]['valor_comissao'], array('nozero' => true)) ?></td>
				<td colspan='7'></td>
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
<?php endif ?>