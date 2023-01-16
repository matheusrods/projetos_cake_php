
<table class="table table-striped">
	<thead>
		<tr>
			<th>Descrição</th>
			<th>E-mail</th>
			<th>Nome</th>
		<tr>
	</thead>
	<tbody>		
		<?php //só mostra os emails do funcionario senao for exame demissional
			if(!empty($infoPedido) && $infoPedido['PedidoExame']['exame_demissional'] != 1): ?>
			<?php foreach($array_funcionarios as $email => $contato) : ?>
				<tr>
					<td>Funcionário:</td>
					<td><?php echo $email; ?></td>
					<td><?php echo $contato; ?></td>
				</tr>			
			<?php endforeach; ?>
		<?php endif; ?>
		<?php foreach($array_fornecedores as $email => $contato) : ?>
			<tr>
				<td>Fornecedor:</td>
				<td><?php echo $email; ?></td>
				<td><?php echo $contato; ?></td>
			</tr>			
		<?php endforeach; ?>
		<?php foreach($array_clientes as $email => $contato) : ?>
			<tr>
				<td>Cliente:</td>
				<td><?php echo $email; ?></td>
				<td><?php echo $contato; ?></td>
			</tr>			
		<?php endforeach; ?>				
	</tbody>
</table>
