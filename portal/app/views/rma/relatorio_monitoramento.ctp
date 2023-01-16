<style type="text/css">
	.ocorrencia{margin-left:23px;}
</style>

<div style = "width:80%;font: 10pt Verdana, Arial;"  align="justify" class="ocorrencia">
	<div id="print">
	  <a style="display:block; float:right;" href="#" onclick="consulta_rm_impressao()" title="Imprimir"><i class="icon-print icon-black"></i></a> 
	</div>
</div>	
<?php foreach ($rma_completo as $rma):?>
	<div style = "width:80%;font: 10pt Verdana, Arial;"  align="justify" class="ocorrencia">
			
		<div class="page-title">
			<center><h3>Relatório de Monitoramento Automático</h3></center>
		</div>
			AC/ <?php echo $rma['transportador_pjur_razao_social']?>&nbsp; - &nbsp;SM:&nbsp;<?php echo $rma['viag_codigo_sm']?><br><br>
			<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Analisando o monitoramento do veículo de vossa empresa,de placa <?php echo $rma['veic_placa']?>  equipamento <?php echo $rma[0]['Terminal']['term_numero_terminal']?>
				e sistema <?php  echo $rma[0]['Sistema']['tecn_descricao'] ?> ,conduzido pelo motorista <?php echo $rma['pess_nome'] ?> às <?php echo $rma['orma_data_cadastro_horas'] ?> do dia <?php echo $rma['orma_data_cadastro_ano']?>
				,identificamos a(s) seguinte(s) ocorr&ecirc;ncias(s):   
			</p>
			<div class="ocorrencia" >
				
				<br><br>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Ocorr&ecirc;ncia:</strong><?="&nbsp;".$rma['trma_descricao']?>.</p><br><br>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Consequencia:</strong><?="&nbsp;".$rma['trma_consequencia']?>.</p><br><br>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Ação:</strong><?="&nbsp;".$rma['trma_acao']?>.</p><br><br>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Sugestão:</strong><?="&nbsp;".$rma['trma_susgestao']?>.</p><br><br>
				
					<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Origem:</strong>&nbsp;<?=$rma['cida_descricao_origem'] ." - ". $rma['esta_sigla_origem'] ?><br><br> </p>
					<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Destino:</strong>&nbsp;<?=$rma['cida_descricao_destino'] ." - ". $rma['esta_sigla_destino'] ?><br><br></p>
					<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Local:</strong>&nbsp;<?=$rma['orma_descricao_local']?><br><br></p>
			</div>
			<br/>	
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$rma['trma_final']?>
			<br/>
		Atenciosamente.<br><br>
        <span style="color: blue; font-size: 12pt"><b>Buonny Sat</b></span><br>
        (11) 5079-2506<br>
        <a href="mailto:buonnysat@buonny.com.br">buonnysat@buonny.com.br</a>
        <br><br>		
	</div>
<?php endforeach ?>

<?php echo $this->Javascript->codeBlock("
	

	function consulta_rm_impressao() {
		
		window.print();
		return false;
	}
	
", false);
?>
		

