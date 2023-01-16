<style>
	body {font-family: Arial; color: #000;}
	table, th, td{ border: 1px solid black; }
	p.titulo {background-color: #3B90DF; color: #fff; text-align: center; font-size: 9pt; margin: 3px; font-weight: bold; padding: 3px;}
	p.corpo {font-size: 9pt; }
</style>

<p class="titulo">Notifica&ccedil;&atilde;o de Insatisfa&ccedil;&atilde;o</p>
<p class="corpo">
	Foi realizado uma pesquisa de satifa&ccedil;&atilde;o e a mesma foi classifica como Insatisfeita<br /><br/><br/>

	<b>Cliente:</b> <?php echo isset($dados['PesquisaSatisfacao']['codigo_cliente']) ? $dados['PesquisaSatisfacao']['codigo_cliente'] : NULL ;?><br/><br/>
	<b>Produto:</b> <?php echo isset($dados['PesquisaSatisfacao']['nome_produto']) ? $dados['PesquisaSatisfacao']['nome_produto'] : NULL ;?><br/><br/><br/>
	<b>Data da Pesquisa:</b> <?php echo isset($dados['PesquisaSatisfacao']['data_pesquisa']) ? $dados['PesquisaSatisfacao']['data_pesquisa'] : NULL ;?><br/><br/><br/>
	<b>Contato:</b> <?php echo isset($dados['ClienteContato']['nome']) ? $dados['ClienteContato']['nome'] : NULL ;?><br/><br/><br/>
	<b>Observa&ccedil;&atilde;o:</b> <?php echo isset($dados['PesquisaSatisfacao']['observacao']) ? $dados['PesquisaSatisfacao']['observacao'] : NULL ;?><br/><br/><br/>
</p>
<br/>
<p class="corpo">Atenciosamente</p>
<p class="corpo"><b>Buonny Projetos e Servi&ccedil;os Ltda</b></p>