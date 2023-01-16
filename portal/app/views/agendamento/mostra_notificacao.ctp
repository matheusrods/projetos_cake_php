	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th class="input-mini">Pedido</th>
	            <th>Nome Funcionário</th>
	            <th>Notificação</th>
	        </tr>
	    </thead>
		<tbody>    
		<?php foreach($lote_pedidos as $pedido) : ?>
			<tr>
				<td><?php echo $pedido['PedidoExame']['codigo']; ?></td>
				<td><?php echo $pedido['Funcionario']['nome']; ?></td>
				<td><a href="/portal/pedidos_exames/notificacao/<?php echo $pedido['PedidoExame']['codigo_func_setor_cargo']; ?>/<?php echo $pedido['PedidoExame']['codigo']; ?>" target="_blank">Notificar</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>