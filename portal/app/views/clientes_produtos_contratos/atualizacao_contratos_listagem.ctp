<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<?php if(isset($contratos)): ?>
		<?php echo $this->BForm->create('ClienteProdutoContrato', array('url' => array('controller' => 'clientes_produtos_contratos', 'action' => 'atualizar_contratos'))); ?>
		<?php echo $this->BForm->hidden('igpm', array('value' => $igpm_acumulado)) ?>
		<div class="well">
			<?php echo $this->Html->link('<i class="icon-refresh icon-white"></i> Atualizar Contratos', array( 'controller' => 'clientes_produtos_contratos', 'action' => 'atualizar_contratos'), array('escape' => false,'id' => 'submit', 'class' => 'btn btn-primary', 'title' =>'Atualizar Contratos'));?>
			<strong>Produto: </strong><?php echo $produto_selecionado ?>
			<strong>Contratos: </strong><?php echo $this->Paginator->params['paging']['ClienteProdutoContrato']['count'] ?>
			<strong>IGPM Acumulado: </strong><?php echo $igpm_acumulado ?>
		</div>  
		<?php if(count($contratos)): ?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th title="Cliente"><?php echo $this->Paginator->sort('Cliente', 'Cliente.razao_social') ?></th>
						<th title="Produto"><?php echo $this->Paginator->sort('Produto', 'Produto.descricao') ?></th>
						<th title="Vencimento"><?php echo $this->Paginator->sort('Data Vencimento', 'ClienteProdutoContrato.data_vigencia') ?></th>
						<th title="Novo Vencimento"><?php echo $this->Paginator->sort('Novo Vencimento', 'ClienteProdutoContrato.nova_data') ?></th>
					</tr>
				</thead>
				<? foreach($contratos as $contrato): ?>
					<tbody>
						<tr>
							<td><?php echo $contrato[0]['razao_social'] ?></td>
							<td><?php echo $contrato[0]['descricao']?></td>
							<td><?php echo substr($contrato['ClienteProdutoContrato']['data_vigencia'], 0, 10) ?></td>
							<td><?php echo date('d/m/Y', strtotime('+1 year', strtotime(str_replace("/", '-', $contrato['ClienteProdutoContrato']['data_vigencia'])))); ?></td>
						</tr>
					</tbody>
				<? endforeach; ?>
			</table>
		<?php endif; ?>
		<?php echo $this->BForm->end();?>
<?php endif; ?>

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