<h5 style="color: #d10808;">Grau de Risco da Empresa: <?php echo $resultado['grau_risco']; ?></h5>
<?php if(in_array($resultado['grau_risco'], array('1', '2'))) : ?>
	<strong style="color: #d10808;">Conforme orientação da NR7, empresas com grau de riscos 1 e 2, estão desobrigadas a renovar os exames do PCMSO na demissão, quando os mesmos estiverem dentro do prazo de <?php echo $resultado['qtd_dias_vencimento']; ?> dias da data do último exame.</strong>
<?php elseif(in_array($resultado['grau_risco'], array('3', '4'))): ?>
	<strong style="color: #d10808;">Conforme orientação da NR7, empresas com grau de riscos 3 e 4, estão desobrigadas a renovar os exames do PCMSO na demissão, quando os mesmos estiverem dentro do prazo de <?php echo $resultado['qtd_dias_vencimento']; ?> dias da data do último exame.</strong>	
<?php endif; ?>

<?php if(isset($resultado['ASO_vencida']) && count($resultado['ASO_vencida'])) : ?>
	<table style="color: #000; margin-top: 15px;">
		<?php foreach($resultado['ASO_vencida'] as $k => $funcionario) : ?>
			<tr>
				<td><?php echo $funcionario['nome']; ?></td>
				<td>Aso realizada: <?php echo $funcionario['data_exame']; ?></td>
				<td>Válidade: <?php echo $funcionario['data_validade']; ?></td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>