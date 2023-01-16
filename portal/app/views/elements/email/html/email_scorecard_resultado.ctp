<?php if( isset($envio_email_scorecard) && $envio_email_scorecard === TRUE ) :?>
<style>
	table, th, td{ border: 1px solid black; }
</style>
<h3>Retorno de Pesquisas</h3>
<p><b>Produto:</b> SCORECARD </p>
<p><b>Cliente:</b> <?php echo $dados['Cliente']['codigo']; ?> - <?php echo $dados['Cliente']['razao_social']; ?></p> 
<p><b>Função:</b> <?php echo $dados['ProfissionalTipo']['descricao']; ?></p> 
<p><b>Nome:</b> <?php echo $dados['Profissional']['nome']; ?>&nbsp; &nbsp;<b>RG:</b><?=$dados['Profissional']['rg'] ?></p>
<?php if(!empty($dados['Veiculo']['placa'])): ?>
<p><b>Veiculo:</b> <?php echo strtoupper($dados['Veiculo']['placa']); ?>
<?php endif; ?> 
<?php if(!empty($dados['Carreta']['placa'])): ?>
	&nbsp; &nbsp;<b>Carreta:</b> <?php echo strtoupper($dados['Carreta']['placa']); ?>
<?php endif; ?> 
<?php if(!empty($dados['Carreta']['placa']) && !empty($dados['Bitrem']['placa'])): ?>
	&nbsp; &nbsp;<b>Bitrem:</b> <?php echo strtoupper($dados['Bitrem']['placa']); ?>
<?php endif; ?> 
<?php if(!empty($dados['Veiculo']['placa'])): ?>
</p>
<?php endif; ?>
<p><b>Tipo de Carga: </b><?=$dados['CargaTipo']['descricao'] ?>&nbsp; &nbsp;<b>Cidade Origem: </b><?= $dados['FichaScorecard']['origem'] ?>&nbsp; &nbsp;  <b>Cidade Destino: </b><?= $dados['FichaScorecard']['destino'] ?></p>
<?php if ( $dados['ParametroScore']['nivel']!='Divergente' and $dados['ParametroScore']['nivel']!='Insuficiente') { ?>
<p><b><font size="4">AVALIAÇÃO SCORECARD: <?php echo $dados['ParametroScore']['nivel']; ?> (Carga m&aacute;xima permitida: R$ <?php echo $this->Buonny->moeda($dados['ParametroScore']['valor']); ?>)</b></font></p> 
<? } ?>
<?php if ( $dados['ParametroScore']['nivel']=='Divergente') { ?>
<p><b><font size="4">AVALIAÇÃO SCORECARD: <?php echo strtoupper('Perfil '.$dados['ParametroScore']['nivel']); ?> - Favor contatar-nos pelos fones (11) 3443-2580 / 2581 ou 2381 </b></font></p> 
<? } ?>
<?php if ( $dados['ParametroScore']['nivel']=='Insuficiente') { ?>
 <p><b><font size="4">AVALIAÇÃO SCORECARD: <?php echo strtoupper('Perfil com Insuficiencia de INFORMAÇÕES - Favor contatar-nos via e-mail controle.pesquisa@buonny.com.br ou via fone 3443-2325, observando instruÇÃO abaixo : '); ?></b></font></p> 
<? } ?>
<?php if ( $dados['ParametroScore']['nivel']!='Divergente' and $dados['ParametroScore']['nivel']!='Insuficiente') { ?>
<p><b>Número da Consulta:</b> <?= $codigo_log_faturamento ?></p>
<?php 	if (@$retorno['mensagem'] == "PERFIL ADEQUADO AO RISCO") { ?>
<?php     if ($this->data['Profissional']['codigo_profissional_tipo']==1) { ?>
<div style="border:0px solid;width:100%;">
	<span style="color:#005D9C;"><font size='4'><b>Validade :</b></span>O Embarque</font>	
</div>
<?php }else{?>
	<div style="border:0px solid;width:100%;">
		<span style="color:#005D9C;"><font size='4'><b>Validade :</b></span>Simples Conferência</font></div><br>
<?php } ?>
<?php } ?>
<? } ?>
<?php if ( $dados['ParametroScore']['nivel'] == 'Insuficiente') { ?>
	<p><b>Informações insuficientes :</b></p>
<?php foreach ($dados['Criterios']['insuficientes'] as $dados_insuf){
	echo  "&nbsp; &nbsp;<b>".$dados_insuf."<b><br>";
	  }
 } ?> 
<?php if ( $dados['ParametroScore']['nivel'] == 'Divergente') { ?>
	<p><b>Informações divergentes :</b></p>
<?php foreach ($dados['Criterios']['divergentes'] as $dados_insuf){
	echo  "&nbsp; &nbsp;<b>".$dados_insuf."<b><br>";
	  }
 } ?>

<p style="color:#F00;">ATEN&Ccedil;&Atilde;O<br/>
	<?php if ( $dados['ParametroScore']['nivel']!='Divergente' and $dados['ParametroScore']['nivel']!='Insuficiente') { ?>
DOCUMENTOS SOB RESPONSABILIDADE DO TRANSPORTADOR: ANTES DE EFETUAR O EMBARQUE FAVOR CONFERIR SE DOCUMENTOS ORIGINAIS DO MOTORISTA EST&Atilde;O EM ORDEM: IDENTIDADE, CNH, DOCUMENTOS DE PORTE OBRIGAT&Oacute;RIO DOS VE&Iacute;CULOS E RNTRC).
<? } ?>
</p>
<p>&Eacute; expressamente proibida a exibi&ccedil;&atilde;o desse documento ao consultado ou a terceiros, e a viola&ccedil;&atilde;o acarretar&aacute; &agrave; contratante e ao funcion&aacute;rio infrator, responsabilidade civil e criminal. </p>
<p>A contrata&ccedil;&atilde;o ou n&atilde;o do(s) profissional(is), &eacute; uma decis&atilde;o da empresa consultante, n&atilde;o cabendo a Gerenciadora de Riscos qualquer responsabilidade sobre esta decis&atilde;o.</p> 
<center>
<p>SETOR DE PESQUISAS</p> 
<p>Todos os STATUS podem sofrer altera&ccedil;&otilde;es.</p> 
<p>E-MAIL AUTOM&Aacute;TICO. FAVOR N&Atilde;O RESPONDER.</p> 
<?php if ( $dados['ParametroScore']['nivel']!='Divergente' and $dados['ParametroScore']['nivel']!='Insuficiente') { ?>
<p>Em caso de d&uacute;vida fone: (11) 3443-2325</p>
</center>
<? } ?>
<?else:
	$logotipo = '<img src="http://www.rhhealth.com.br/assets/img/logo-rhhealth.png" /><br />';		
	    $corpo_mail_bruto =
	    'Retorno de Pesquisa - '.$dados['ProfissionalTipo']['descricao'].' <br><br>' .
	    'Cliente: '.$dados['Cliente']['razao_social'].' <br><br>' .
	    'Produto: SCORECARD <br><br>' .
	    'Nome: '.$dados['ProfissionalLog']['nome'].' <br><br>';
	    if( $dados['FichaScorecard']['codigo_profissional_tipo'] < 6 ){
			if( !empty($dados['Veiculo']['placa']) )
		    	$corpo_mail_bruto .= 'Veiculo: '. strtoupper($dados['Veiculo']['placa']).' <br><br>';
			if( !empty($dados['Carreta']['placa']) )		    
		    	$corpo_mail_bruto .= 'Carreta: '.strtoupper($dados['Carreta']['placa']).' <br><br>';
			if( !empty($dados['Bitrem']['placa']) )		    
		    	$corpo_mail_bruto .= 'Bitrem: ' .strtoupper($dados['Bitrem']['placa']).' <br><br>';		    
		}
		if( $dados['ParametroScore']['codigo']  == ParametroScore::INSUFICIENTE ){
			$corpo_mail_bruto .= '<b>Status: PERFIL COM INSUFICIÊNCIA DE INFORMAÇÕES - Favor contatar-nos via e-mail controle.pesquisa@buonny.com.br ou via fone 11 5079 2325, observando instrução abaixo:</b><br><br>';
			$informacoesinsuficientes = NULL;
			if( !empty( $dados['Criterios']['insuficientes'] )) {
				foreach( $dados['Criterios']['insuficientes'] as $info ) {
					$informacoesinsuficientes .= "<li>".$info['Criterio']['descricao'].": ".$info['StatusCriterio']['descricao'] ."</li>";
				}
				if($informacoesinsuficientes)
					$informacoesinsuficientes = '<ul>'.$informacoesinsuficientes.'</ul><br /><br />';
			}
			$corpo_mail_bruto .= $informacoesinsuficientes;
			$corpo_mail_bruto .= 
			'Atenção: É expressamente proibida a exibição desse documento ao consultado ou a terceiros, e a violação acarretará à ' .
			'contratante e ao funcionário infrator, responsabilidade civil e criminal. ' .
			'A contratação ou não do(s) profissional(is), é uma decisão da empresa consultante, não cabendo a Gerenciadora de Riscos ' .
			'qualquer responsabilidade sobre esta decisão. ' .
			'<br /><br /><center>SETOR DE PESQUISAS <br>' .
			'Todos os STATUS podem sofrer alterações. <br><br>' .
			'E-MAIL AUTOMÁTICO. FAVOR NÃO RESPONDER.';
		} elseif ( $dados['ParametroScore']['codigo'] == ParametroScore::DIVERGENTE ){
			$corpo_mail_bruto .='<b>Status: PERFIL DIVERGENTE - Favor contatar-nos pelos fones (11)3443-2580/2581 ou 2381</b><br><br>';
			if( !empty( $dados['Criterios']['divergentes'] )) {
				$informacoes_divergentes = NULL;
				foreach( $dados['Criterios']['divergentes'] as $info ) {
					$informacoes_divergentes .= "<li>". $info['Criterio']['descricao'].": ".$info['StatusCriterio']['descricao'] ."</li>";
				}
				if($informacoes_divergentes)
					$informacoes_divergentes = '<ul>'.$informacoes_divergentes.'</ul><br /><br />';
				$corpo_mail_bruto .= $informacoes_divergentes;
				$corpo_mail_bruto .=
				'Atenção: É expressamente proibida a exibição desse documento ao consultado ou a terceiros, e a violação acarretará à ' .
				'contratante e ao funcionário infrator, responsabilidade civil e criminal. ' .
				'A contratação ou não do(s) profissional(is), é uma decisão da empresa consultante, não cabendo a Gerenciadora de Riscos ' .
				'qualquer responsabilidade sobre esta decisão. ' .
				'<br /><br /><center>SETOR DE PESQUISAS <br>' .
				'Todos os STATUS podem sofrer alterações. <br><br>' .
				'E-MAIL AUTOMÁTICO. FAVOR NÃO RESPONDER. <br>';
			}            
	    } else {
			$corpo_mail_bruto .= '<b>Status: PERFIL ADEQUADO AO RISCO</b><br><br>';
			if( $codigo_log_faturamento )
				$corpo_mail_bruto .= 'Consulta Número: '.$codigo_log_faturamento.'<br><br>';

			$validate_ficha = ($dados['FichaScorecard']['codigo_profissional_tipo'] == 1 ? 'O EMBARQUE' : substr($dados['FichaScorecard']['data_validade'], 0, 10 ) );
			$corpo_mail_bruto .=
			    'Validade: '.$validate_ficha.' <br><br>' .
			    '<b>ATENÇÃO</b><br>' .
			    'É expressamente proibida a exibição desse documento ao consultado ou a terceiros, e a violação acarretará à<br>' .
			    'contratante e ao funcionário infrator, responsabilidade civil e criminal. <br><br>' .
			    'A contratação ou não do(s) profissional(is), é uma decisão da empresa consultante, não cabendo a ' .
			    'Gerenciadora de Riscos<br>qualquer responsabilidade sobre esta decisão. <br><br>' .
			    '<center>SETOR DE PESQUISAS <br>' .
			    'Todos os STATUS podem sofrer alterações. <br><br>' .
			    'E-MAIL AUTOMÁTICO. FAVOR NÃO RESPONDER. <br>' .
			    'Em caso de dúvida fone: (11) 3443-2325 </center>';		    
	    }	   
		echo $corpo_mail_bruto;?>
<?endif;?>