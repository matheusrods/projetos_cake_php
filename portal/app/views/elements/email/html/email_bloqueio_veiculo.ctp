<style>
	body {font-family: Arial; color: #000;}
	table, th, td{ border: 1px solid black; }
	p.titulo {background-color: #3B90DF; color: #fff; text-align: center; font-size: 9pt; margin: 3px; font-weight: bold; padding: 3px;}
	p.corpo {font-size: 9pt; }
</style>
<p class="titulo">
	COMUNICADO DE BLOQUEIO DE VE&Iacute;CULO: <? echo strtoupper($dados['TVeicVeiculo']['veic_placa'])?>
</p>
<p class="corpo">
	Prezados Srs,<br/><br/><br/>
	Informamos  que o ve&iacute;culo abaixo foi bloqueado.<br/><br/>
	Desta forma, o ve&iacute;culo ficar&aacute;  impossibilitado da emiss&atilde;o de Solicita&ccedil;&atilde;o de Monitoramento.
	Em caso de duvidas entrar em contato com a &aacute;rea de Gerenciamento de Riscos.<br/><br/>
</p>
<p class="corpo">
	Usu&aacute;rio que realizou o bloqueio: <?=$nome_usuario?><br/>
	Email: <?=$email_usuario?><br/>
</p>
<p class="corpo">
	<b>Placa: </b><? echo strtoupper($dados['TVeicVeiculo']['veic_placa'])?><br/>
	<b>Alvo Origem: </b><? echo (!empty($alvo['TRefeReferencia']['refe_descricao']) ? $alvo['TRefeReferencia']['refe_descricao'] : 'TODOS')?><br/>
	<b>Tecnologia: </b><? echo (!empty($dados_veiculo['TTecnTecnologia']['tecn_descricao']) ? $dados_veiculo['TTecnTecnologia']['tecn_descricao'] : '')?><br/>
	<b>Rastreador: </b><? echo (!empty($dados_veiculo['TTermTerminal']['term_numero_terminal']) ? $dados_veiculo['TTermTerminal']['term_numero_terminal'] : '')?><br/>
</p>
<br/>
<p class="corpo">Atenciosamente</p>
<p class="corpo"><b>Buonny Projetos e Servi&ccedil;os Ltda</b></p>