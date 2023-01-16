<style>
	table, th, td{ border: 1px solid black; }
</style>
<h3>
	<?php if(date("H") < 12): ?>
		Bom dia!
	<?php elseif(date("H") < 18): ?>
		Boa tarde!
	<?php else: ?>
		Boa noite!
	<?php endif; ?>
</h3>
<p>Foi realizado o check list para o veiculo de placa 
<strong><?php echo $veiculo['TVeicVeiculo']['veic_placa'] ?></strong>
, da empresa <strong><?php echo $cliente['Cliente']['razao_social'] ?></strong> 
(TECNOLOGIA: <strong><?php echo $veiculo['TTecnTecnologia']['tecn_descricao'] ?></strong> ) 
, dia <?php echo date('d/m/Y') ?>.
</p>

<p>
	Foi constatado que os atuadores e sensores estao em pleno funcionamento.
</p>

<p>Check List Aprovado.</p>

<p>Atenciosamente.</p>