<?php if(isset($dados) && count($dados)) : ?>

	<div class='well'>
	    <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => 'clientes_fornecedores', 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
	</div>

	<?php echo $paginator->options(array('update' => 'div.lista')); ?>
	
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="input-small">Código Cliente</th>
				<th class="input-large">Cliente</th>
				<th class="input-large">Cod. Prestador</th>
				<th class="input-large">Nome Prestador</th>
				<th class="input-large">Data Inclusão</th>
				<th class="input-mini">Status</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($dados as $registro): ?>
				<tr>
					<td><?php echo $registro['ClienteFornecedor']['codigo_cliente']?></td>
					<td><?php echo $registro['Cliente']['nome_fantasia']?></td>
					<td><?php echo $registro['ClienteFornecedor']['codigo_fornecedor']?></td>
					<td><?php echo $registro['Fornecedor']['razao_social']?></td>
					<td><?php echo $registro['ClienteFornecedor']['data_inclusao']?></td>
					<td style>
						<?php if($registro['Fornecedor']['ativo'] == 1): ?>
	                        <span class="badge badge-empty badge-success" title="Ativado"></span>
	                    <?php else: ?>
	                        <span class="badge badge-empty badge-important" title="Inativo"></span>
	                    <?php endif; ?>
	            	</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['GrupoEconomicoCliente']['count']; ?></td>
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

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php echo $this->Javascript->codeBlock("
	
    $('[data-toggle=\"tooltip\"]').tooltip();

    function listagem(){
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'clientes_fornecedores/listagem_cliente_por_fornecedor/' + Math.random());
    }
");
?>

