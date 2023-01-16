<style>
	table, th, td{ border: 1px solid black; }
</style>
<h3>Prezado(a)</h3>
<p>Por favor tome as devidas providencias para sanar os problemas encontrados nos arquivos de integração encontrados abaixo:</p>

<table>
	<thead>
		<tr>
			<td style="width:50px" >Loadplan</td>
			<td style="width:200px" >Arquivo</td>
			<td>Erros</td>
		</tr>
	</thead>
	<tbody>
		
		<?php foreach ($emailError as $arq => $error ): ?>
			<tr>
				<td><?php echo $error['loadplan'] ?></td>
				<td><?php echo $arq ?></td>
				<td><?php echo Comum::implodeRecursivo( '<br />',$error['error'] ) ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<br />
<p>Obrigado !!!</p>
