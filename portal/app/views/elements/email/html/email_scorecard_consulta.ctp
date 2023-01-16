<style>
	table, th, td{ border: 1px solid black; }
</style>
<h3>Retorno de Consulta</h3>
<p><b>Produto:</b> SCORECARD </p>
<p><b>Cliente:</b> <?=$dados_cliente['Cliente']['codigo']?> - <?=$dados_cliente['Cliente']['razao_social']?></p> 
<p><b>Nome:</b> <?=$retorno_consulta['ultima_ficha']['ProfissionalLog']['nome']?>&nbsp; &nbsp;<b>RG:</b><?=$retorno_consulta['ultima_ficha']['ProfissionalLog']['rg'] ?></p>
<?php if(!empty($filtros['placa_veiculo'])): ?>
	<p><b>Veiculo:</b> <?=strtoupper($filtros['placa_veiculo'])?>
<?php endif; ?>
<?php if(!empty($filtros['placa_carreta'])): ?>
	&nbsp; &nbsp;<b>Carreta:</b> <?php echo strtoupper($filtros['placa_carreta']); ?>
<?php endif; ?> 
<?php if(!empty($filtros['placa_bitrem'])): ?>
	&nbsp; &nbsp;<b>Bitrem:</b> <?php echo strtoupper($filtros['placa_bitrem']); ?>
<?php endif; ?>
<?php if(!empty($filtros['placa_veiculo'])): ?>
</p>
<?php endif; ?>
<p><b>Tipo de Carga:</b> <?=$tipo_carga['CargaTipo']['descricao']?>&nbsp; &nbsp;<b>Cidade Origem:</b> <?=$filtros['cidade_origem']?>&nbsp; &nbsp;  <b>Cidade Destino:</b> <?=$dados_consulta['cidade_destino']?></p>
<span style="color:#005D9C;"><font size='4'>AVALIAÇÃO SCORECARD: </font></span>
<span style="color: <?php echo $retorno_consulta['mensagem_retorno']['TipoOperacao']['mensagem_cor'] ; ?>">
<font size='4'><?php echo $retorno_consulta['mensagem_retorno']['TipoOperacao']['mensagem'] ?></font></span>		
<?php		
$profissional_sem_score = array( 10,9,5,6,7,8);		
if(  $envio_email_scorecard ) {
	if ( $retorno_consulta['mensagem_retorno']['TipoOperacao']['mensagem'] =='PERFIL ADEQUADO AO RISCO' ) {
		if( !in_array( $retorno_consulta['ultima_ficha']['FichaScorecard']['codigo_profissional_tipo'], $profissional_sem_score )) {
			//Não mostra Score de alguns profissionais
			if(!in_array( $retorno_consulta['ultima_ficha']['FichaScorecard']['codigo_profissional_tipo'], $profissional_sem_score )) { ?>			
				<h2>Classificação do Profissional: </h2>
				<span class="label <?php echo ($retorno_consulta['ultima_ficha']['ParametroScore']['valor'] <= 0 ? 'label-important' : 'label-success')?>"><?=$retorno_consulta['ultima_ficha']['ParametroScore']['nivel']?></span>
				<b>(Carga máxima permitida: R$ <?=$this->Buonny->moeda( $retorno_consulta['ultima_ficha']['ParametroScore']['valor'] ) ?>)</b><br/>
			<?php } ?>
		<?php } ?>			
	<?php } ?>
<?php } ?>
<?php if ( $retorno_consulta['ultima_ficha']['ParametroScore']['pontos'] > 0 ) { ?>
   <p>
   	<b>Número da Consulta:</b>
	<?php 
		if( $retorno_consulta['mensagem_retorno']['TipoOperacao']['mensagem'] != "PERFIL ADEQUADO AO RISCO" ) {
   			echo "<b><font size=3>"."XXXXX"."</font></b>";
	   	}else{
           echo "<b><font size=3>". $retorno_consulta['codigo_log_faturamento'] ."</font></b>";
       }?>
	</p>
<?php if ( $retorno_consulta['mensagem_retorno']['TipoOperacao']['mensagem']  == "PERFIL ADEQUADO AO RISCO") { ?>
	<?php if ($retorno_consulta['ultima_ficha']['FichaScorecard']['codigo_profissional_tipo']==1) { ?>
        <div style="border:0px solid;width:100%;">
          	<span style="color:#005D9C;"><font size='4'><b>Validade: </b></span>O Embarque</font>
		</div>
    <?php }else{?>
         <div style="border:0px solid;width:100%;">          
          	<span style="color:#005D9C;"><font size='4'><b>Validade: </b></span> Simples Conferência
		</font></div><br>
    <?php } ?>
 <?php } ?>
<? } ?>
<?php
	if ( $retorno_consulta['ultima_ficha']['ParametroScore']['pontos'] == 0 ) { ?>
  	<p><b>Informações insuficientes: </b></p>
	<?php  
		foreach ($campos_insuficientes as $dados_insuf ){
			echo "&nbsp; &nbsp;<b>".$dados_insuf."<b><br>";
		}
	} ?>
<p style="color:#F00;">ATEN&Ccedil;&Atilde;O<br/>
	<?php if ( $retorno_consulta['ultima_ficha']['ParametroScore']['pontos'] > 0 ) { ?>
		DOCUMENTOS SOB RESPONSABILIDADE DO TRANSPORTADOR: ANTES DE EFETUAR O EMBARQUE FAVOR CONFERIR SE DOCUMENTOS ORIGINAIS DO MOTORISTA EST&Atilde;O EM ORDEM: IDENTIDADE, CNH, DOCUMENTOS DE PORTE OBRIGAT&Oacute;RIO DOS VE&Iacute;CULOS E RNTRC).
<? } ?>
</p>
<p>&Eacute; expressamente proibida a exibi&ccedil;&atilde;o desse documento ao consultado ou a terceiros, e a viola&ccedil;&atilde;o acarretar&aacute; &agrave; contratante e ao funcion&aacute;rio infrator, responsabilidade civil e criminal. </p>
<p>A contrata&ccedil;&atilde;o ou n&atilde;o do(s) profissional(is), &eacute; uma decis&atilde;o da empresa consultante, n&atilde;o cabendo a Gerenciadora de Riscos qualquer responsabilidade sobre esta decis&atilde;o.</p> 
<center>
<p>SETOR DE PESQUISAS</p> 
<p>Todos os STATUS podem sofrer altera&ccedil;&otilde;es.</p>
<p>E-MAIL AUTOM&Aacute;TICO. FAVOR N&Atilde;O RESPONDER.</p> 
<?php if ( $retorno_consulta['ultima_ficha']['ParametroScore']['pontos'] > 0 ) { ?>
<p>Em caso de d&uacute;vida fone: (11) 3443-2325</p>
</center>
<? } ?>