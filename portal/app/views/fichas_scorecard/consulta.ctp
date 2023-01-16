<? if( empty($dados_consulta) ): ?>
<div class = 'form-procurar'>
	<div class="well">
		<?php echo $this->BForm->create('FichaScorecard', array('url' => array('controller' => 'fichas_scorecard', 'action' => $this->action)));?>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_cliente_usuario_cliente($this, $usuarios, null, true, 'Código') ?>
			<?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
		</div>
		<div class="row-fluid inline">
			<?php if( $codigo_cliente ) {?>  
			<?php echo $this->BForm->input("codigo_cliente", array('label' => 'Código', 'class' => 'input-mini just-number', 'readonly'=>true, 'value' => $codigo_cliente)) ?>
			<?php } else { ?>
			<?php echo $this->BForm->input("Usuario.codigo_documento", array('label' => 'CPF/CNPJ', 'class' => 'input-medium', 'readonly'=>true)) ?>
			<?php } ?>
			<?php echo $this->Buonny->input_embarcador_transportador($this, $embarcadores, $transportadores, 'codigo_cliente', 'Cliente', true, 'FichaScorecard', null, false) ?>
		</div>  
		<div class="row-fluid inline">
			<?php if (!empty($cnpj)){
				echo $this->BForm->input("Cliente.codigo_documento", array('label' => 'CNPJ', 'class' => 'input-medium cnpj', 'readonly'=>true, 'value'=>$cnpj)); 
			}?>
			<?php if (!empty($razao_social)){
				echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true, 'value'=>$razao_social)) ;
			}?>  
		</div>  
		<div class="row-fluid inline">
			<?php echo $this->BForm->input("codigo_documento", array('label' => 'CPF', 'class' => 'input-medium cpf', 'id'=>'ProfissionalCodigoDocumento', 'after' => $html->link('...', "javascript:carrega_profissional_por_cpf($codigo_cliente)", array('id' =>'avancar','class' => 'btn btn-search-ellipsis', 'title' => 'Buscar dados')) )) ?>
			<?php echo $this->BForm->input("Profissional.codigo", array('type' => 'hidden', 'readonly'=>true)) ?>
			<?php echo $this->BForm->input("nome", array('label' => 'Nome do Profissional', 'class' => 'input-large just-letters', 'readonly'=>true,'id'=>'ProfissionalNome')) ?>    
			<?php echo $this->BForm->input('placa_veiculo', 
			array( 'label' =>'Placa do veículo','class' => 'placa-veiculo input-small', 'value' => (isset($this->data['placa']) ? $this->data['placa'] : NULL) ) ) ?>
			<?php echo $this->BForm->input('placa_carreta', 
			array( 'label' =>'Placa da carreta', 'class' => 'placa-veiculo input-small', 'value' => (isset($this->data['placa']) ? $this->data['placa'] : NULL) ) ) ?>
			<?php echo $this->BForm->input('placa_bitrem', 
			array( 'label' =>'Placa do bitrem', 'class' => 'placa-veiculo input-small', 'value' => (isset($this->data['placa']) ? $this->data['placa'] : NULL) ) ) ?>    
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input("codigo_carga_tipo", array('label' => 'Tipo de carga', 'class' => 'input-large', 'empty' => 'Tipo', 'options'=>$carga_tipos)) ?>
		</div>
		<div style="width:800px;">
			<div style="float:left;width:50%;">
				<h5>Origem</h5>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input('cidade_origem', array('class' => 'input-large ui-autocomplete-input', 'empty' => 'Cidade', 'label' => 'Cidade', 'for' =>'FichaScorecardCodigoEnderecoCidadeCargaOrigem')) ?>
					<?php echo $this->BForm->input('codigo_endereco_cidade_carga_origem', array('type' => 'hidden')) ?>
					<?php echo $this->BForm->input('codigo_estado_origem',    array('class' => 'input-large', 'type' => 'hidden', 'empty' => 'Cidade', 'label' => false)) ?>
				</div>
			</div>  
			<div style="float:right;width:50%;">
				<h5>Destino</h5>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input('cidade_destino', array('class' => 'input-large ui-autocomplete-input','empty' => 'Cidade', 'label' => 'Cidade', 'for'=>'FichaScorecardCodigoEnderecoCidadeCargaDestino' )) ?>
					<?php echo $this->BForm->input('codigo_endereco_cidade_carga_destino', array('type' => 'hidden')) ?>
					<?php echo $this->BForm->input('codigo_estado_destino',    array('class' => 'input-large', 'type' => 'hidden', 'empty' => 'Cidade', 'label' => false)) ?>
				</div>
			</div>  
		</div>
		<br />
		<?php echo $this->BForm->submit('Consultar', array('div' => false, 'class' => 'btn btn-success', 'id' => 'btnConsulta')); ?>
		<?php echo $html->link('Limpar', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end() ?>
	</div>
</div>
<? else:?>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<style type="text/css">

		div.wrap {
			width: 600px;
			margin: 0 auto;
		}
		.print{
			text-decoration: none;
			left: 50%;			
			position: absolute;			
		}
		.voltar{
			text-decoration: none;
			left: 50%;			
			position: absolute;			
		}
		a:link {text-decoration: none;}
	</style>

	<style type="text/css" media="print">
		.btnImp {
			display:none;			
		}
		.voltar {
			display:none;	
		}
	</style>
</head>
<div class="retorno" style="border:0px solid;width: 900px; margin:0 auto;text-align:left;">
	<p class="print">
		<a class="btnImp" style="background-image: url('/portal/img/twiter/glyphicons-halflings.png');background-position: -96px -48px; background-repeat: no-repeat;" title="Imprimir" href="javascript:self.print()">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</a>
	</p>
	<div style="border:0px solid;width:100%;height:50px;">
		<img src="http://www.rhhealth.com.br/assets/img/logo-rhhealth.png" align="left" width="200" height="70">	
	</div>		
	
	<div style="border:0px solid;">
		<h3 style="border:0px solid;margin: 20px 0;text-align:left;">Retorno de Consulta</h3>
	</div>

	<div style="border:0px solid;">
		<div style="border:0px solid;width:100%;">
			<p>
			<span style="color:#005D9C;font-weight:bold;">Data: </span><?=date("d/m/Y")?>
			<span style="color:#005D9C;font-weight:bold;">Hora: </span> <?=date("H:i")?>
		   </p>
		</div>
	
		<div style="border:0px solid;width:100%;">
			<p>
			<span style="font-weight:bold;">Razão Social:</span>
			<?=$dados_cliente['Cliente']['codigo']?> - <?=$dados_cliente['Cliente']['razao_social']?>
			</p>
			<p>
				<span style="font-weight:bold;">Profissional: </span><?=$retorno_consulta['ultima_ficha']['ProfissionalLog']['nome']?>
				<span style="font-weight:bold;">RG :</span> <?=$retorno_consulta['ultima_ficha']['ProfissionalLog']['rg'] ?>
			</p>
			<p>
				<span style="font-weight:bold;">Veículo:</span> <?=strtoupper($filtros['placa_veiculo'])?>
				<?if( !empty($filtros['placa_carreta'])):?>
				<span style="font-weight:bold;">Carreta:</span> <?=strtoupper($filtros['placa_carreta'])?>
				<?endif;?>
				<?if( !empty($filtros['placa_bitrem'])):?>
				<span style="font-weight:bold;">BiTrem:</span> <?=strtoupper($filtros['placa_bitrem'])?>
				<?endif;?>
			</p>
			<p>
				<span style="font-weight:bold;">Tipo da Carga: </span><?=$tipo_carga['CargaTipo']['descricao']?>
				<span style="font-weight:bold;">Origem: </span><?=$filtros['cidade_origem']?>
				<span style="font-weight:bold;">Destino: </span><?=$dados_consulta['cidade_destino']?>
			</p>
		</div>
	</div>
	<div style="border:0px solid;width:100%;margin:0 auto;text-align:left;">	
		<span style="color:#005D9C;"><font size='4'>Status: </font></span>
		<span style="color: <?php echo $retorno_consulta['mensagem_retorno']['TipoOperacao']['mensagem_cor'] ; ?>">
		<font size='4'><?php echo $retorno_consulta['mensagem_retorno']['TipoOperacao']['mensagem'] ?></font></span>		
		<?php		
		$profissional_sem_score = array( 10,9,5,6,7,8);	
		//debug	($retorno_consulta['mensagem_retorno']['TipoOperacao']);
		if ( $retorno_consulta['mensagem_retorno']['TipoOperacao']['mensagem'] =='PERFIL ADEQUADO AO RISCO' && FichaScorecard::ENVIA_EMAIL_SCORECARD) {
			if( !in_array( $retorno_consulta['ultima_ficha']['FichaScorecard']['codigo_profissional_tipo'], $profissional_sem_score )) {
				//Não mostra Score de alguns profissionais
				if(!in_array( $retorno_consulta['ultima_ficha']['FichaScorecard']['codigo_profissional_tipo'], $profissional_sem_score )) { ?>			
					<h2>Classificação do Profissional: </h2>
					<span class="label <?php echo ($retorno_consulta['ultima_ficha']['ParametroScore']['valor'] <= 0 ? 'label-important' : 'label-success')?>"><?=$retorno_consulta['ultima_ficha']['ParametroScore']['nivel']?></span>
					<? $pneus = $pneus_pontuacao[$retorno_consulta['ultima_ficha']['ParametroScore']['nivel']];?>
					<?php for($i = 1; $i <= 5; $i++): ?>
					<?php   if( $i <= $pneus )
					     		echo $this->Html->image('pneu_dourado.png', array('alt' => ''));
					     	else
					     		echo $this->Html->image('pneu_cinza.png', array('alt' => ''));?>
					<?php endfor; ?>
					<b>(Carga máxima permitida: R$ <?=$this->Buonny->moeda( $retorno_consulta['ultima_ficha']['ParametroScore']['valor'] ) ?>)</b><br/>
				<?php } ?>
			<?php } ?>			
		<?php } ?>			
	</div>		
	<br>
	<div style="border:0px solid;width:100%;">		
		<?if( $retorno_consulta['codigo_log_faturamento'] ) { ?>
		<span style="color:#005D9C;"><font size='4' ><b>Número de Consulta: </b></font></span>
		  <?php if ( $retorno_consulta['mensagem_retorno']['TipoOperacao']['mensagem'] != "PERFIL ADEQUADO AO RISCO" ) {
			       echo "<b><font size=3>"."XXXXX"."</font></b>";
	           }else{
	               echo "<b><font size=3>". $retorno_consulta['codigo_log_faturamento'] ."</font></b>";
	           }
		 ?>
	</div><br>
		<?php } ?>
		<?php if ( $retorno_consulta['mensagem_retorno']['TipoOperacao']['mensagem']  == "PERFIL ADEQUADO AO RISCO") { ?>
		<?php if( $retorno_consulta['ultima_ficha']['FichaScorecard']['codigo_profissional_tipo'] == 1 ):?>
	        <div style="border:0px solid;width:100%;">
	          	<span style="color:#005D9C;"><font size='4'><b>Validade: </b></span>O Embarque</font>
			</div>
		<?php else: ?>	
			<div style="border:0px solid;width:100%;">
			<span style="color:#005D9C;"><font size='4'><b>Validade: </b></span>Simples Conferência
			</div>			
		<?php endif; ?>	
	     <?php } ?>
	     <br />
    	<div style="width:100%;text-align:justify;">
			<!--<span style="font-weight:bold;"><?php //echo $retorno['descricao']; ?></br></span> -->
			<span><p><?php echo $retorno_consulta['mensagem_retorno']['TipoOperacao']['observacao']; ?></p></span>
		</div>
		<br />
        <div style="width:100%;text-align:justify;">
				<!--<span style="font-weight:bold;"><?php //echo $retorno['descricao']; ?></br></span> -->
			<span style="font-weight:bold;"><p><b>ATENÇÃO : De acordo com o contrato de prestação de serviços de Teleconsult é expressamente proibida a exibição deste documento a consultados ou a terceiros, e, a violação desta norma, acarretará à contratante e ao funcionário infrator, responsabilidade civil e criminal.A contratação ou não do profissional consultado, é uma decisão da empresa consultante, não cabendo à Buonny qualquer responsabilidade sobre esta decisão.</b></p></span>
		</div>
        <br />
	<br />
	<div class="voltar" id ="voltar" style="border:0px solid;">
		<?php echo $html->link('Voltar', array('action' => 'consulta'), array('class' => 'btn')); ?>	
	</div>
</div>
<br />
<?php  echo $this->Javascript->codeBlock('jQuery(document).ready(function(){
	$("div .well").hide();
	$(".page-title").hide();
})');?>



<? endif;?>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>
<?php echo $this->Javascript->codeBlock("
	 $(document).ready(function() {  	 	
	 	carrega_profissional_por_cpf($codigo_cliente)
		$('#FichaScorecardCodigoUsuario').click(function(event){      
			if ( parseInt( $(this).val() ) > 0) {
				carregar_usuario( $(this) );
			}
		}); 

	$('#print').click(function(){
		window.print();      
	});

	$('#btnConsulta').click(function(){
		var div = jQuery('div.lista');
		//bloquearDiv(div);
		if(!validaAssinaturaCliente(3)){
			$('.alert-error').remove();    
			$(\".form-procurar\").prepend(\"<div class='alert alert-error'>Serviço não disponível para o embarcador e transportador selecionados. Favor entrar em contato com o Departamento Comercial.</div>\");
			return false;
		}else{
			//div.load(baseUrl + 'fichas_scorecard/consulta_profissional/' + Math.random()); 
		}
		
	});

	$(function() {
		$('.ui-autocomplete-input').autocomplete({        
			source: baseUrl + 'enderecos/autocompletar/',
			focus: function(){return false;},
			minLength: 3,
			select: function( event, ui ) {
				nome_cidade   = ui.item.label;
				codigo_cidade = ui.item.value;        
				codigo_cidade_hidden = $(this).attr('for');        
				$(this).val( nome_cidade );
				$('#'+codigo_cidade_hidden).val( codigo_cidade );
				return false;
			}});
	});


	setup_mascaras();
	setup_codigo_cliente();
	$('#limpar-filtro').click(function(){
		$('.form-procurar :input').not(':button, :submit, :reset, :hidden').val('');
		//$('.form-procurar form').submit(); 
	});
});", false);?>