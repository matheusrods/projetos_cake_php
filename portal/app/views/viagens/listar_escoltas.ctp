<table class='table table-striped'>
	<thead>
		<th>Empresa</th>
		<th>Equipe</th>
		<th>Telefone</th>
		<th>Placa</th>
		<th>Tecnologia</th>
		<th>Versão</th>
		<th>Número Terminal</th>

		<th></th>
	</thead>

	<tbody>
		<?php foreach ($escoltas as $escolta): ?>		
		<tr>
			<td><?php echo $escolta['TPessEscolta']['pess_nome'] ?></td>
			<td><?php echo $escolta['TVescViagemEscolta']['vesc_equipe'] ?></td>
			<td><?php echo $escolta['TVescViagemEscolta']['vesc_telefone']; ?></td>
			<td><?php echo strtoupper($escolta['TVescViagemEscolta']['vesc_placa']); ?></td>
			<td><?php echo $escolta['TTecnTecnologia']['tecn_descricao']; ?></td>
			<td><?php echo $escolta['TVtecVersaoTecnologia']['vtec_descricao']; ?></td>
			<td><?php echo $escolta['TVescViagemEscolta']['vesc_numero_terminal']; ?></td>
			<td>
				<?php echo $this->Html->link('', array('action' => 'editar_escolta', $escolta['TVescViagemEscolta']['vesc_codigo'], rand()), array('onclick' => 'return open_dialog(this, "Editar Escolta", 560)', 'title' => 'Editar Escolta', 'class' => 'icon-edit'));?>
			</td>
		</tr>
	<?php endforeach ?>
</tbody>
</table>