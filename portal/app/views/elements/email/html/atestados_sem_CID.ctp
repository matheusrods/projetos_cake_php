<h3>Atestados sem CID:</h3>

<table>
	<tr>
		<td>Cliente:</td>
		<td>Funcionario:</td>
		<td>CPF:</td>
		<td>Data Atestado:</td>
		<td>Periodo Licença:</td>
		<td>Médico</td>
		<td>Local</td>
	</tr>
	<?php foreach($atestados_sem_CID as $key => $atestado) : ?>
		<tr>
			<td><?php echo $atestado['Cliente']['razao_social']; ?></td>
			<td><?php echo $atestado['Funcionario']['nome']; ?></td>
			<td><?php echo $atestado['Funcionario']['cpf']; ?></td>
			<td><?php echo $atestado['Atestado']['data_inclusao']; ?></td>
			<td>
				<?php echo $atestado['Atestado']['data_afastamento_periodo']; ?> <?php echo $atestado['Atestado']['hora_afastamento'] != '00:00:00.0000000' ? " (" . substr($atestado['Atestado']['hora_afastamento'], 0, -11) . ") " : ''; ?><br />
				até<br />
				<?php echo $atestado['Atestado']['data_retorno_periodo']; ?> <?php echo $atestado['Atestado']['hora_retorno'] != '00:00:00.0000000' ? " (" . substr($atestado['Atestado']['hora_retorno'], 0, -11) . ") " : ''; ?> 
			</td>
			<td>
				<?php echo $atestado['Medico']['nome']; ?>
				<?php echo $atestado['ConselhoProfissional']['descricao']; ?>
				<?php echo $atestado['Medico']['numero_conselho']; ?>/<?php echo $atestado['Medico']['conselho_uf']; ?>
			</td>
			<td>
				<?php if(!empty($atestado['Atestado']['endereco']) && trim($atestado['Atestado']['endereco']) != "") : ?>
					<?php echo $atestado['Atestado']['endereco']; ?>, 
					<?php echo $atestado['Atestado']['numero']; ?>
					<br />
					<?php echo (!empty($atestado['Atestado']['complemento']) && trim($atestado['Atestado']['complemento']) != "") ? "(".$atestado['Atestado']['complemento'].")" : ""; ?>
					<?php echo $atestado['Atestado']['bairro']; ?>
				<?php endif; ?>
				
				<?php if(!empty($atestado['EnderecoCidade']['descricao']) && trim($atestado['EnderecoCidade']['descricao']) != "") : ?>
					<br />
					<?php echo $atestado['EnderecoCidade']['descricao']; ?>/<?php echo $atestado['EnderecoEstado']['descricao']; ?>				
				<?php endif; ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>
