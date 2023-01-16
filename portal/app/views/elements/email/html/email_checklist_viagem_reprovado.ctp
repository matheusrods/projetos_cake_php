<style>
	table, th, td{ border: 1px solid black; }
</style>
<h3>
	Prezados(as) 
	<?php if(date("H") < 12): ?>
		Bom dia!
	<?php elseif(date("H") < 18): ?>
		Boa tarde!
	<?php else: ?>
		Boa noite!
	<?php endif; ?>
</h3>
<p>Autocarga abaixo temporariamente reprovado coleta LGE Cajamar</p>
<p>
	<strong>Motivo:</strong>
	<? if (is_array($dados['motivos']) && count($dados['motivos'])>0): ?>
	<ul>
		<?foreach ($dados['motivos'] as $motivo) : ?>
			<li><?=$motivo?></li>
		<? endforeach;?>
	</ul>
	<? endif; ?>
</p>
<p>
	<strong>Transportadora:</strong><?=$dados['ClienteTransportador']['razao_social']?>
</p>
<p>
	<strong>Placa:</strong><?=$dados['TVeicVeiculo']['veic_placa']?>
</p>
<p>
	<strong>Carreta:</strong><?=$dados['TVeicVeiculoCarreta']['veic_placa']?>
</p>
<p>
	<strong>Nome:</strong><?=$dados['TPessPessoa']['pess_nome']?>
</p>
<p>
	<strong>CPF:</strong><?=$dados['TPfisPessoaFisica']['pfis_cpf']?>
</p>
<p>
	OBS: Solicitamos que efetue a manutenção ou a substituição do veículo para não comprometer o agendamento..
</p>

<p>Atenciosamente.</p>