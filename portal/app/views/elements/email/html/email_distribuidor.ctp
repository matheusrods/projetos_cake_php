<style>
	table, th, td{ border: 1px solid black; }
</style>
<h3>Sr. Responsável</h3>
<p>Por favor tome as devidas providencias para sanar os problemas encontrados nas viagens listadas abaixo para que possamos concluir a distribuição automática de forma coerente e acertada.</p>

<table>
	<thead>
		<tr>
			<td style="width:50px" >Cod. SM</td>
			<td>Motivo</td>
		</tr>
	</thead>
	<tbody>
		<?php if($data): ?>
		<?php foreach ($data as $mot): ?>
			<tr>
				<td><?php echo $mot['codigo_sm'] ?></td>
				<td><?php echo $mot['descricao'] ?></td>
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		
	</tbody>
</table>
<br />
<p>Obrigado !!!</p>
