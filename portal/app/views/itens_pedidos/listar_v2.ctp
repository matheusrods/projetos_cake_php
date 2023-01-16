<?php if (empty($cliente)): ?>
	<div class='form-procurar'>	
		<div class='well'>
			<?php echo $this->BForm->create('ItemPedido', array('autocomplete' => 'off', 'url' => array('controller' => 'itens_pedidos', 'action' => 'listar_v2'))) ?>
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
		<?php echo $this->BForm->create('ItemPedido', array('autocomplete' => 'off', 'url' => array('controller' => 'itens_pedidos', 'action' => 'listar_v2'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('mes_faturamento', array('options' => $mes_ano, 'class' => 'input-medium', 'label' => false)); ?>
	        <?php echo $this->BForm->input('ano_faturamento', array('label' => false, 'placeholder' => 'Ano','class' => 'input-mini numeric just-number', 'title' => 'Ano de Faturamento')) ?>

	        <?php echo $this->BForm->input('codigo_cliente', array('label' => false, 'type' => 'hidden', 'value' =>  $cliente['Cliente']['codigo'])) ?>
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
			<?php echo $html->link('Limpar', 'listar_v2', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		</div>
	</div>
				
	<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'itens_pedidos', 'action' => 'incluir_pedido_v2', $cliente['Cliente']['codigo']), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Pedido'));?>
	</div>
	
	<?php if( !empty($pedidos) ): ?>

		<table class='table'>
			<thead>
				<th>Pedido</th>
				<th>Mês</th>
				<th>Ano</th>
				<th>Cond.Pgto</th>
				<th>Dt.Integração</th>
				<th class='input-small numeric'>Total</th>
				<th class='action-icon'>Status</th>
				<th colspan="2">Ações</th>
			</thead>
			<tbody>			
				<?php foreach($pedidos as $key => $value):?>
					<tr>
						<td><?php echo $value['Pedido']['codigo']; ?></td>
						<td><?php echo $meses[$value['Pedido']['mes_referencia']]; ?></td>
						<td><?php echo $value['Pedido']['ano_referencia']; ?></td>
						<td><?php echo strtoupper($value['Pedido']['descricao_cond_pagto']); ?></td>
						<td><?php echo $value['Pedido']['data_integracao']; ?></td>
						<td class='input-small numeric'><?php echo $this->Buonny->moeda($value['Pedido']['valor_total']); ?></td>												

						<td class='action-icon'> 
							<? if(!empty($value['Pedido']['nota_cancelada'])):?> 
								<span class="badge-empty badge badge-important" data-toggle="tooltip" title="nota cancelada: <?=$value['Pedido']['nota_cancelada']?> "="Inativo"></span> 
							<? else: ?>
								<span class="badge-empty badge badge-success" data-toggle="tooltip" title="Ativo"></span><? endif;?>
						</td>
						<?php if(empty($value['Pedido']['data_integracao'])): ?>

							<td class='action-icon'><?php echo $this->Html->link('', array('controller' => 'itens_pedidos', 'action' => 'editar_v2', $value['Pedido']['codigo_cliente_pagador'], $value['Pedido']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?></td>
							<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "excluir_pedido({$cliente['Cliente']['codigo']},{$value['Pedido']['codigo']})")) ?></td>
						<?php else: ?>
							<td colspan="2"> &nbsp; </td>
						<?php endif;?>
					</tr>
				<?php endforeach; ?>
				
			</tbody>
		</table>
	<?php endif; ?>

	<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
	<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>

	<script type="text/javascript">
	$(document).ready(function() {
		
	});
	function excluir_pedido(cliente,pedido) {

		swal({
			type: 'warning',
			title: 'Atenção',
			text: 'Tem certeza que deseja excluir este pedido?',
			showCancelButton: true,
			cancelButtonText: 'Cancelar',
			confirmButtonText: 'Excluir',
			showLoaderOnConfirm: true
		}, function(){
			
			$('body').append($('<div>', {class: 'ajax-loader'}));

			$.ajax({
                type: 'POST',        
                url: baseUrl + 'itens_pedidos/excluir_pedido_v2/'+cliente+'/'+pedido,
                data: {cliente: cliente, pedido: pedido},
                dataType : 'json',
                success : function(data){ 

                	$('.ajax-loader').remove();

                    if(data) {

                        swal({
                            type: 'success',
                            title: 'Sucesso',
                            text: 'O pedido foi excluídos com sucesso.'
                        }, function(){
							location.reload();
						});
                    }
                    else{
                        swal({
                            type: 'warning',
                            title: 'Atenção',
                            text: 'O pedido não pode ser excluídos. Tente novamente'
                        });
                    }
                },
                error : function(){

                	$('.ajax-loader').remove();

                    swal({
                        type: 'warning',
                        title: 'Atenção',
                        text: 'O pedido não pode ser excluídos. Tente novamente'
                    });
                }
            });

		});
	}
	</script>
	
<?php endif ?>


