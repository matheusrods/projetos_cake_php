<style>
	body {font-family: Arial; color: #000;}
	table, th, td{ border: 1px solid black; }
	p.titulo {background-color: #3B90DF; color: #fff; text-align: center; font-size: 9pt; margin: 3px; font-weight: bold; padding: 3px;}
	p.corpo {font-size: 9pt; }
</style>

<p class="titulo">EXCLUS&Atilde;O DE V&Iacute;NCULO</p>
<p class="corpo">
	<b>Data:</b> <?php echo date("d/m/Y H:i:s");?><br/><br/>
	<b>Cliente:</b> <?php echo $dados_email[0]['codigo_cliente'];?><br/><br/>
	<b>Raz&atilde;o Social:</b> <?php echo $dados_email[0]['razao_social'];?><br/><br/><br/>
	Por sua solicita&ccedil;&atilde;o, estaremos excluindo os v&iacute;culos referentes aos profissionais conforme abaixo:<br /><br/><br/>
	<b>Profissionais que ser&atilde;o desvinculados:</b><br /><br/>
	<?php foreach ($dados_email as $key=> $dados ) { ?>
	<?php echo $dados['codigo_documento']." - ".$dados['nome'] ?><br /><br/>
	<?php } ?>	
</p>
<br/>
<p class="corpo">Atenciosamente</p>
<p class="corpo"><b>Buonny Projetos e Servi&ccedil;os Ltda</b></p>