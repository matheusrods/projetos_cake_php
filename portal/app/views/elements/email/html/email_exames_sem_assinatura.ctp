
<div style="font-family:verdana;font-size:12px;">
	Prezado (a),<br />
	A vers&atilde;o do PCMSO da unidade <b><?php echo $Unidade[0]['codigo'] . " - " . $Unidade[0]['nome_fantasia']; ?></b> foi finalizada por&eacute;m os seguintes exames aplicados n&atilde;o possuem assinatura:
	<ul>
		<?php 
		foreach($Exames as $key => $exame) { 
			echo "<li><b>".$key." - ".$exame."</b></li>"; 
		}
		?>					
	</ul>
	<br />
	Favor tomar as a&ccedil;&otilde;es necess&aacute;rias.
</div> 