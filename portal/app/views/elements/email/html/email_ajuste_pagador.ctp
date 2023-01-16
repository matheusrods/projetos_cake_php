<style>
	table.mail_pagador,table.mail_pagador td{ border: 1px solid black; }
</style>
<h3>Troca de Pagadores nas SMs</h3>
<table class='mail_pagador'>
	<thead>
		<tr>
			<td style="width:50px" >SM</td>
			<td>Antigo</td>
			<td>Novo</td>
			<td>Sistema Origem</td>
		</tr>
	</thead>
	<tbody>
		<?php if($dados_pagador): ?>
			<?php foreach ($dados_pagador as $dado): ?>
				<tr>
					<td><?php echo $dado[0]['sm'] ?></td>
					<td><?php echo $dado[0]['cliente_pagador_antigo'] ?></td>
					<td><?php echo $dado[0]['codigo_cliente_pagador'] ?></td>
					<td><?php echo $dado[0]['sistema_origem'] ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
<h3>Quantidade de SMs Órfans</h3>
<strong><?= $qtd_sm_sem_pagador ?></strong>
<h3>SMs sem Contrato</h3>
<table class='mail_pagador'>
	<thead>
		<tr>
			<td style="width:50px" >SM</td>
			<td>Cadastro</td>
			<td>Código</td>
			<td>Embarcador</td>
			<td>Código</td>
			<td>Transportador</td>
			<td>Código Pagador</td>
			<td>Sistema Origem</td>
		</tr>
	</thead>
	<tbody>
		<?php if($dados_contratos): ?>
			<?php foreach ($dados_contratos as $dado): ?>
				<tr>
					<td><?php echo $dado['Recebsm']['sm'] ?></td>
					<td><?php echo $dado['0']['dta_receb'] ?></td>
					<td><?php echo $dado['Embarcador']['codigo'] ?></td>
					<td><?php echo $dado['Embarcador']['razao_social'] ?></td>
					<td><?php echo $dado['Transportador']['codigo'] ?></td>
					<td><?php echo $dado['Transportador']['razao_social'] ?></td>
					<td><?php echo $dado['Recebsm']['cliente_pagador'] ?></td>
					<td><?php echo $dado['Recebsm']['sistema_origem'] ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		
	</tbody>
</table>