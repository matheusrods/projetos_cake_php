<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<?php $total_paginas = $this->Paginator->numbers(); ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>    

<table class="table table-striped tablesorter">
    <thead>
		<th class="input-mini"><?php echo $this->Html->link('Código', 'javascript:void(0)') ?></th>
		<th><?php echo $this->Html->link('Nome', 'javascript:void(0)') ?></th>
		<th><?php echo $this->Html->link('CNPJ', 'javascript:void(0)') ?></th>
		<th class="sorter-false">&nbsp;</th>
    </thead>
    <tbody>
	<?php foreach ($clientes as $cliente): ?>
        <tr>
            <td class="input-mini"><?php echo $cliente['Cliente']['codigo'] ?></td>
            <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
            <td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
            <td class="pagination-centered"><?php echo $html->link('', array('action' => 'editar_clientes_vips', $cliente['Cliente']['codigo']), array('class' => 'icon-edit btn-modal', 'title' => 'Editar')) ?></td>
		</tr>
    <?php endforeach; ?>
     <tfoot>
        <?php if( isset($clientes) ): ?>
            <tr>
                <td><strong>Total</strong></td>
                <td colspan="6" class="input-xlarge"><strong>
                    <?php 
                        if($this->Paginator->counter('{:count}') > 1)
                            echo $this->Paginator->counter('{:count}')." Clientes Vips";
                        else
                            echo $this->Paginator->counter('{:count}')." Clientes Vips";
                    ?></strong>
                </td>
            </tr>
        <?php  endif;?>
    </tfoot>


    </tbody>

</table>

<div class='row-fluid'>
    <div class='numbers span6'>
        <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    </div>
    <div class='counter span6'><?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?></div>
</div>



<?php echo $this->Js->writeBuffer(); ?>

<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
	$.tablesorter.addParser({
		debug:true,
		id: "brazil", 
		is: function(s) { 
			// return false so this parser is not auto detected 
			// poderia ser detectado pelo simbolo do real R$
			return false;
		},
		format: function(s) { 
		   s = s.replace(/\./g,"");
		   s = s.replace(/\,/g,".");
		   return $.tablesorter.formatFloat(s.replace(new RegExp(/[^0-9.-]/g),""));
		}, 
		type: "numeric"
	});
	
	jQuery("table.table").tablesorter({
		headers: {
			2: {sorter: "brazil"},
			3: { sorter: false }
		},
		widgets: ["zebra"]
	});
});', false);
?>