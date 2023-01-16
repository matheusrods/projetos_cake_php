<?php echo $this->Paginator->options(array('update'=>'.lista')); ?>
<div class="well">
    <strong>De:</strong> 01/<?= $mes.'/'.$ano;?> <strong>até</strong> 31/<?= $mes.'/'.$ano;?> 
</div>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
        	
            <th class="input-mini"><?php echo $this->Paginator->sort('Código','Cliente.codigo')?></th>
            <th class="input-xxlarge"><?php echo $this->Paginator->sort('Cliente','Cliente.razao_social')?></th>
            <th class="input-mini numeric"><?php echo $this->Paginator->sort('Taxa Adm.(R$)','ItemPedido.valor_taxa_bancaria')?></th>
        </tr>
    </thead>
    <tbody>
        
        
        <?php foreach($itens_pedidos as $item_pedido): ?>
            
                                
            <tr>
                <td class="input-mini"><?php  echo $item_pedido['Cliente']['codigo']?></td> 
                <td class="input-xlarge"><?php  echo $item_pedido['Cliente']['razao_social']?></td>
                <td class="numeric"><?php echo $this->Buonny->moeda($item_pedido['ItemPedido']['valor_taxa_bancaria']); ?></td>
            </tr>
	    <?php endforeach; ?>
    </tbody>
    <tfoot>
        	<tr>
            	<td><strong>Total: </strong><?php echo $this->params['paging']['ItemPedido']['count']; ?></td>
            	<td></td>
            	<td class='numeric'><?php echo $this->Buonny->moeda($total[0][0]['valor_taxa_bancaria']);?></td>
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
</table>
<?php echo $this->Js->writeBuffer();?>