<?php if (count($dados)): ?>
	<div class="well">
		<span class="pull-right">
            <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>   
        </span>
	</div>
	<?php 
	    echo $paginator->options(array('update' => 'div.comissoes_por_corretora_analitico_listagem')); 
	?>
	<table class='table table-striped' style='max-width:none;white-space:nowrap'>
		<thead>
			<th>Código</th>
			<th>Cliente</th>
			<th class='numeric'>Valor Unitário</th>
			<th class='numeric'>Quantidade</th>
			<th class='numeric'>Valor</th>
			<th class='numeric'>Impostos (%)</th>
			<th class='numeric'>Valor Líquido</th>
			<th class='numeric'>Comissão (%)</th>
			<th class='numeric'>Valor Comissão</th>
			<th class='numeric'>De</th>
			<th class='numeric'>Até</th>
			<th>Produto</th>
			<th>Serviço</th>
		</thead>
		<tbody>
			<?php foreach ($dados as $dado): ?>
				<tr style='word-wrap:none'>
					<td><?= $dado[0]['cliente_codigo'] ?></td>
					<td><?= $dado[0]['cliente_nome'] ?></td>
					<td class='numeric'><?= (!empty($dado[0]['valor_unitario']) ? $this->Buonny->moeda($dado[0]['valor_unitario'], array('nozero' => true)) : "") ?></td>
					<td class='numeric'><?= $dado[0]['quantidade'] ?></td>
					<td class='numeric'><?= (!empty($dado[0]['valor_servico']) ? $this->Buonny->moeda($dado[0]['valor_servico'], array('nozero' => true)) : "") ?></td>
					<td class='numeric'><?= (!empty($dado[0]['percentual_impostos']) ? $this->Buonny->moeda($dado[0]['percentual_impostos'], array('nozero' => true)) : "") ?></td>
					<td class='numeric'><?= (!empty($dado[0]['valor_servico_liquido']) ? $this->Buonny->moeda($dado[0]['valor_servico_liquido'], array('nozero' => true)) : "") ?></td>
					<td class='numeric'><?= (!empty($dado[0]['percentual_comissao']) ? $this->Buonny->moeda($dado[0]['percentual_comissao'], array('nozero' => true)) : "") ?></td>
					<td class='numeric'><?= (!empty($dado[0]['valor_comissao']) ? $this->Buonny->moeda($dado[0]['valor_comissao'], array('nozero' => true)) : "") ?></td>
					<td class='numeric'><?= (!empty($dado[0]['preco_de']) ? $this->Buonny->moeda($dado[0]['preco_de'], array('nozero' => true)) : "") ?></td>
					<td class='numeric'><?= (!empty($dado[0]['preco_ate']) ? $this->Buonny->moeda($dado[0]['preco_ate'], array('nozero' => true)) : "") ?></td>
					<td><?= utf8_encode($dado[0]['produto_nome']) ?></td>
					<td><?= utf8_encode($dado[0]['servico_nome']) ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan='4'></td>
				<td class='numeric'><strong><?= $this->Buonny->moeda($totais[0][0]['valor_servico'], array('nozero' => true)) ?></strong></td>
				<td></td>
				<td class='numeric'><strong><?= $this->Buonny->moeda($totais[0][0]['valor_servico_liquido'], array('nozero' => true)) ?></strong></td>
				<td></td>
				<td class='numeric'><strong><?= $this->Buonny->moeda($totais[0][0]['valor_comissao'], array('nozero' => true)) ?></strong></td>
				<td colspan='4'></td>
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
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
		$('.numbers a[id^=\"link\"]').bind('click', function (event) { bloquearDiv($('.comissoes_por_corretora_analitico_listagem')); });
    });", false);
?>