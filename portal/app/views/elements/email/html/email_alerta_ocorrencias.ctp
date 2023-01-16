<style>
	body {font-family: Arial; color: #000;}
	table, th, td{ border: 1px solid black; }
	p.titulo {background-color: #3B90DF; color: #fff; text-align: center; font-size: 9pt; margin: 3px; font-weight: bold; padding: 3px;}
	p.corpo {font-size: 9pt; }
</style>
<p class="titulo">
	Ocorr&ecirc;ncia lan&ccedil;ada para a SM - <?= $sm?>
</p>
<p class="corpo">
	Prezados Srs,<br/><br/><br/>
	Lan&ccedil;ada nova ocorr&ecirc;ncia:<br/><br/>
	Usu&aacute;rio: <?= $usuario;?> &nbsp;&nbsp;&nbsp;&nbsp;
	data <?= substr($data_horario, 0,10);?>&nbsp;&nbsp;&nbsp;&nbsp;
	hora <?= substr($data_horario, 10);?>
	<br/>
</p>
<p class="corpo">
	<b>Placa: <?= $veiculo['TVeicVeiculo']['veic_placa'];?></b><br/>
	<b>Origem: <?= $refe_origem['TRefeReferencia']['refe_descricao'];?></b><br/>
	<b>Destino: <?= $refe_destino['TRefeReferencia']['refe_descricao'];?></b><br/>
</p>
<br/>
<p class="corpo">
	Ocorr&ecirc;ncia : <?= $data['TVocoViagemOcorrencia']['voco_descricao'];?>
</p>
<br/>
<p class="corpo">Atenciosamente</p>
<p class="corpo"><b>Buonny Projetos e Servi&ccedil;os Ltda</b></p>