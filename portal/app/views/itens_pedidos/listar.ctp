<?php if (empty($cliente)): ?>
	<div class='form-procurar'>	
		<div class='well'>
			<?php echo $this->BForm->create('ItemPedido', array('autocomplete' => 'off', 'url' => array('controller' => 'itens_pedidos', 'action' => 'listar'))) ?>
			<div class="row-fluid inline">
				<?php echo $this->Buonny->input_codigo_cliente($this); ?>
			</div>
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
			<?php echo $this->BForm->end();?>
		</div>
	</div>
<?php else: ?>
	<div class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo']; ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social']; ?>
		<strong>Ano: </strong><?= date('Y'); ?>
	</div>

    <div class='well'>
        <?php echo $this->BForm->create('ItemPedido', array('autocomplete' => 'off', 'url' => array('controller' => 'itens_pedidos', 'action' => 'listar'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('mes_pedido', array('options' => $mes_ano, 'class' => 'input-medium', 'label' => false)); ?>
            <?php echo $this->BForm->input('ano_pedido', array('label' => false, 'placeholder' => 'Ano','class' => 'input-mini numeric just-number', 'title' => 'Ano Pedido Assinatura')) ?>

            <?php echo $this->BForm->input('codigo_cliente', array('label' => false, 'type' => 'hidden', 'value' =>  $cliente['Cliente']['codigo'])) ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
            <?php echo $html->link('Limpar', 'listar', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        </div>
    </div>

	<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'itens_pedidos', 'action' => 'incluir_pedido', $cliente['Cliente']['codigo']), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Pedido'));?>
	</div>
	
	<?php if( !empty($pedidos) ): ?>

		<table class='table'>
			<thead>
				<th>Pedido</th>
				<th>Mês</th>
				<th>Ano</th>
				<th>Produto</th>
				<th>Cond.Pgto</th>
				<th>Dt.Integração</th>
				<th class='input-small numeric'>Qtd</th>
				<th class='input-small numeric'>Val. Unitário</th>
				<th class='input-small numeric'>Total</th>
				<th class='action-icon'>Status</th>
				<th class='action-icon'></th>
				<th class='action-icon'></th>
			</thead>
			<tbody>			
			<tbody>	
				<?php foreach($pedidos as $key => $value):?>


					<?php
					$exibirDel = '';
					$exibirEdit = '';
					if(empty($value['Pedido']['data_integracao'])) {
						$exibirDel = $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "javascript:excluir_pedido({$cliente['Cliente']['codigo']},{$value['Pedido']['codigo']})"));
						$exibirEdit = $this->Html->link('', array('controller' => 'itens_pedidos', 'action' => 'editar', $value['Pedido']['codigo_cliente_pagador'], $value['ItemPedido']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));
					}
					?>

					<tr>
						<td><?php echo $value['Pedido']['codigo']; ?></td>
						<td><?php echo $meses[$value['Pedido']['mes_referencia']]; ?></td>
						<td><?php echo $value['Pedido']['ano_referencia']; ?></td>
						<td><?php echo $value['Produto']['descricao']; ?></td>
						<td><?php echo strtoupper($value['CondPag']['descricao']); ?></td>
						<td><?php echo $value['Pedido']['data_integracao']; ?></td>
						<td class='input-small numeric'><?php echo $value['ItemPedido']['quantidade']; ?></td>
						<td class='input-small numeric'><?php echo $this->Buonny->moeda($value[0]['valor_unitario']); ?></td>
						<td class='input-small numeric'><?php echo $this->Buonny->moeda($value['ItemPedido']['valor_total']); ?></td>
						<td class='action-icon'> 
							<? if(!empty($value['Pedido']['nota_cancelada'])):?> 
								<span class="badge-empty badge badge-important" data-toggle="tooltip" title="nota cancelada: <?=$value['Pedido']['nota_cancelada']?> "="Inativo"></span> 
							<? else: ?>
								<span class="badge-empty badge badge-success" data-toggle="tooltip" title="Ativo"></span><? endif;?>
						</td>
						<td class='action-icon'><?= $exibirEdit ?></td>
						<td class='action-icon'><?= $exibirDel ?></td>
					</tr>
				<?php endforeach; ?>
				
			</tbody>
		</table>

	<?php endif; ?>
	
	<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
	<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
	
<?php endif ?>


<?php 
echo $this->Javascript->codeBlock("
	function excluir_pedido(cliente,pedido) {
		if (confirm('Deseja excluir este pedido?'))
			location.href = '/portal/itens_pedidos/excluir_pedido/' + cliente + '/' + pedido;
	}
	"); ?>