<?php if(!empty($fornecedor)): ?>
	<?php 
	    echo $paginator->options(array('update' => 'div.lista')); 
	    $total_paginas = $this->Paginator->numbers();
	?>

	<div class='row-fluid inline'>
		<div id="fornecedor" class='well'>
			<strong>Código: </strong><?= $fornecedor['Fornecedor']['codigo'] ?>
			<strong>Fornecedor: </strong><?= $fornecedor['Fornecedor']['razao_social'] ?>
		</div>
	</div>
<?php endif;?>

<?php if(!empty($dados)):?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th class="input-small">Código Cliente</th>
				<th class="input-large">Cliente</th>
				<th class="input-large">Data Inclusão</th>
				<th class="input-mini">Status</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($dados as $registro): ?>
				<tr>
					<td><?php echo $registro['ClienteFornecedor']['codigo_cliente']?></td>
					<td><?php echo $registro['Cliente']['nome_fantasia']?></td>
					<td><?php echo $registro['ClienteFornecedor']['data_inclusao']?></td>
					<td style>
						<?php if($registro['ClienteFornecedor']['ativo']): ?>
	                        <span class="badge badge-empty badge-success" title="Ativado"></span>
	                    <?php else: ?>
	                        <span class="badge badge-empty badge-important" title="Inativo"></span>
	                    <?php endif; ?>
	            	</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
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

