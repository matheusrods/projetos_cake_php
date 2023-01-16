<style>
	table.mail_pagador,
	table.mail_pagador td{ border: 1px solid #CCC;border-spacing:0};
</style>
<h3><?= $descricao_alerta?></h3>
<table class='mail_pagador'>
	<thead>
		<tr>
			<td style="width:250px">Data Consulta</td>
			<td style="width:450px">Consulta</td>			
		</tr>
	</thead>
	<tbody>
		<?php foreach ($consulta as $dado): ?>
			<tr>
				<td><?php echo $dado[0]['query_start']?></td>
				<td><?php echo $dado[0]['current_query']?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
