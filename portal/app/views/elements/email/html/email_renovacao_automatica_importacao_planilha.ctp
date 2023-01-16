<h3>
	Renovacao Automatica Importacao Planilha
</h3>
<p>
	<strong>Cliente</strong>: <?php echo $dados_nome_arquivo[2] ?><br />
	<strong>Arquivo</strong>: <?php echo $dados_nome_arquivo[3] ?><br />
	<strong>Data da Importacao</strong>: <?php echo date('d/m/Y H:i:s',strtotime(substr($dados_nome_arquivo[0], 7,14))) ?><br />
	<strong>Total Profissionais</strong>: <?php echo $qtd_profissionais ?><br />
	<strong>Profissionais incluidos para renovacao</strong>: <?php echo $qtd_add_para_renovar ?><br />
	<strong>Usuario</strong>: <?php echo $usuario['Usuario']['apelido'] ?><br />
</p>