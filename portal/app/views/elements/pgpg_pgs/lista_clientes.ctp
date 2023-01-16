<?php 
	echo $paginator->options(array('update' => 'div.lista-clientes')); 
?>
<table id='clientesx' class='table table-striped'>
	<thead>
		<th class='input-xxlarge'>Razão Social</th>
		<th class='input-medium'>Tipo</th>
		<th class='input-medium'>CNPJ</th>
	</thead>
	<tbody>
		<?php if ($clientes_pgr): ?>
			<?php foreach ($clientes_pgr as $cliente) : ?>
				<tr>
					<td class='input-xxlarge'><?=$cliente['TPjurPessoaJuridica']['pjur_razao_social']?></td>
					<td class='input-medium'><?=(isset($arrayEmbarcadorTransportador[$cliente['TEmbarcadorTransportador']['tipo_cliente']]) ? $arrayEmbarcadorTransportador[$cliente['TEmbarcadorTransportador']['tipo_cliente']] : '') ?></td>
					<td class='input-medium'><?=Comum::formatarDocumento($cliente['TPjurPessoaJuridica']['pjur_cnpj'])?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
    <tfoot>
        <tr>
            <td colspan = "3"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['TPgpgPg']['count']; ?></td>
        </tr>
    </tfoot>   				
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
    	<?php echo $this->Paginator->prev('Página Anterior', Array('url'=>Array('codigo_pgr'=>$this->data['TPgpgPg']['pgpg_codigo'])), null,  array('class' => 'disabled paginacao_anterior')); ?>
        <?php echo $this->Paginator->numbers(); ?>
    	<?php echo $this->Paginator->next('Próxima Página', Array('url'=>Array('codigo_pgr'=>$this->data['TPgpgPg']['pgpg_codigo'])), null, array('class' => 'disabled paginacao_proximo')); ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>
