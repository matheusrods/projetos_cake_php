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
<p>CHECKLIST - RECUSADO Cliente: <? echo $razao_social['TPjurPessoaJuridica']['pjur_razao_social']?>Placa: <? echo $placa['TVeicVeiculo']['veic_placa']?>Status: <?php echo $motivo[$dados['TOveiOcorrenciaVeiculo']['ovei_mcch_codigo']]?>, favor entrar em contato com a Central de Monitoramento, celula de Checklist.</p>


<p>Checklist RECUSADO.</p>

<p>Atenciosamente.</p>