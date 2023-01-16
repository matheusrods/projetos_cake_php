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
, da empresa <strong><?php echo $cliente['Cliente']['razao_social']; ?></strong> 
(TECNOLOGIA: <strong><?php echo $veiculo['TTecnTecnologia']['tecn_descricao'] ?></strong> ) 
, dia <?php echo date('d/m/Y') ?>.
</p>

<p>
	Foi constatado que os seguintes itens nao estao funcionando corretamente:<br />
	<?php foreach ($icve as $item): ?>
	- <?php echo $item['TPpadPerifericoPadrao']['ppad_descricao'] ?> <br />
	<?php endforeach; ?>
</p>
<p>
	Diante ao exposto, o veiculo supra citado <strong style="color:red">NAO ESTA LIBERADO</strong> pela central de monitoramento para realizacao da viagem. Recomendamos urgente manutencao do (s) dispositivo (s) danificado (s).
</p>
<p>
	Check List <strong style="color:red">REPROVADO</strong>.<BR />
	<?php if($ambiente == Ambiente::SERVIDOR_PRODUCAO): ?>
		<a href="http://portal.buonny.com.br/portal/viagens/justificar_inicio_sem_checklist/<?php echo $veiculo['TVeicVeiculo']['veic_placa'] ?>/<?php echo $cliente['Cliente']['codigo'] ?>" target="_blank">Autorizar inicio sem checklist</a>
	<?php elseif($ambiente == Ambiente::SERVIDOR_HOMOLOGACAO): ?>
		<a href="http://tstportal.buonny.com.br/portal/viagens/justificar_inicio_sem_checklist/<?php echo $veiculo['TVeicVeiculo']['veic_placa'] ?>/<?php echo $cliente['Cliente']['codigo'] ?>" target="_blank">Autorizar inicio sem checklist</a>
	<?php else: ?>
		<a href="http://portal.localhost/portal/viagens/justificar_inicio_sem_checklist/<?php echo $veiculo['TVeicVeiculo']['veic_placa'] ?>/<?php echo $cliente['Cliente']['codigo'] ?>" target="_blank">Autorizar inicio sem checklist</a>
	<?php endif; ?>
</p>


<p>Atenciosamente.</p>