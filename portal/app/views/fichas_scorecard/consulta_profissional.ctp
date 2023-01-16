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
<?php
//debug($this->data);
//debug($this->data['FichaScorecardVeiculo']);
//debug($dados_pesquisa);

$placa_veiculo  = isset($this->data['FichaScorecardVeiculo'][0]['Veiculo']['placa']) ? Comum::formatarPlaca($this->data['FichaScorecardVeiculo'][0]['Veiculo']['placa']) : NULL;
$placa_carreta  = isset($this->data['FichaScorecardVeiculo'][1]['Veiculo']['placa']) ? Comum::formatarPlaca($this->data['FichaScorecardVeiculo'][1]['Veiculo']['placa']) : NULL;
$placa_bitrem  = isset($this->data['FichaScorecardVeiculo'][2]['Veiculo']['placa']) ? Comum::formatarPlaca($this->data['FichaScorecardVeiculo'][2]['Veiculo']['placa']) : NULL;


$profissional   = $dados_pesquisa['Profissional']['nome'];
$cpf_profissional = Comum::formatarDocumento($dados_pesquisa['Profissional']['codigo_documento']);
$codigo_cliente = $dados_pesquisa['Cliente']['codigo'];
$razao_social_cliente  = $dados_pesquisa['Cliente']['razao_social'];
$transportador  = $dados_pesquisa['Transportador']['razao_social'];
$embarcador     = $dados_pesquisa['Embarcador']['razao_social'];
$tipo_carga     = isset($dados_pesquisa['CargaTipo'])     ? $dados_pesquisa['CargaTipo']     : NULL;
$carga_valor    = isset($dados_pesquisa['CargaValor'])    ? $dados_pesquisa['CargaValor']    : NULL;
// $estado_origem  = isset($dados_pesquisa['EstadoOrigem'])  ? $dados_pesquisa['EstadoOrigem']  : NULL; 
$cidade_origem  = isset($dados_pesquisa['CidadeOrigem'])  ? $dados_pesquisa['CidadeOrigem']  : NULL;
// $estado_destino = isset($dados_pesquisa['EstadoDestino']) ? $dados_pesquisa['EstadoDestino'] : NULL;
$cidade_destino = isset($dados_pesquisa['CidadeDestino']) ? $dados_pesquisa['CidadeDestino'] : NULL;
$profissional_valorado = array('Agregado'=>2,'Carreteiro'=>1,'Funcionario/Motorista'=>3,'Proprietario'=>4);
 $valorar = '';
if(isset($this->data['Profissional']['codigo_profissional_tipo']) && in_array($this->data['Profissional']['codigo_profissional_tipo'],$profissional_valorado)) { 
  
  if ($retorno['mensagem']=='PERFIL ADEQUADO AO RISCO') {
           $valorar ="NAOVALORAR";  
  }
}


echo $this->Buonny->link_css('fichas_scorecard');
echo $this->Buonny->link_css('app');
?>

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
			<?php if (!empty($codigo_cliente) && !empty($razao_social_cliente)){
			?>
				<p>
					<span style="font-weight:bold;">Razão Social:</span>
					<?=$codigo_cliente?> - <?=$razao_social_cliente?>
				</p>
			<?php } ?>	
			<p><span style="font-weight:bold;">Função :</span> 
				<?=$this->data['Profissional']['descricao_profissional_tipo'] ?></p>
			<?php //if (!empty($profissional) && !empty($cpf_profissional)){
			?>
			<p>
				<span style="font-weight:bold;">Profissional:</span>
				<?=$profissional?>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<span style="font-weight:bold;">RG :</span> 
				<?=@$this->data['Profissional']['rg'] ?>
				
			</p>

            <?php //} ?>
			
					<p>
						<?php if (!empty($placa_veiculo)){
			?>
						<span style="font-weight:bold;">Veiculo:</span> <?=$placa_veiculo?>
					<?php } ?>
					    <?php if (!empty($placa_carreta)){
			?>
						<span style="font-weight:bold;">Carreta:</span> <?=$placa_carreta?>
					<?php } ?>
					<?php if (!empty($placa_bitrem)){ ?>
						<span style="font-weight:bold;">BiTrem:</span> <?=$placa_bitrem?>
					</p> 
			        <?php } ?>
			<p>
				<span style="font-weight:bold;">Tipo da Carga:</span> <?=$tipo_carga?>
				<!-- span style="font-weight:bold;"> Valor:</span --> <? //= $carga_valor ?>
				<span style="font-weight:bold;">Origem:</span> <?=$cidade_origem?><!-- / <?=$estado_origem?>-->
				<span style="font-weight:bold;">Destino:</span> <?=$cidade_destino?><!-- / <?=$estado_destino?> -->
			</p>
		</div>
	</div>
	<div style="border:0px solid;width:100%;margin:0 auto;text-align:left;">
	   <?php if ($valorar =='NAOVALORAR') { ?>	
                
					<span style="color:#005D9C;"><font size='4'><b>Classificação do Profissional:</b></font></span>
					<span style="color: <?php echo $retorno['mensagem_cor']; ?>"></span>
				


	   <?php }else{ ?> 
				
					<span style="color:#005D9C;"><font size='4'>Status :</font></span>
					<span style="color: <?php echo $retorno['mensagem_cor']; ?>">
						<font size='4'><?php echo $retorno['mensagem']?></font></span>
				
        <?php }  ?> 

		
		<?php
		$nivel  = isset($classificacao['ParametroScore']['nivel'])  ? $classificacao['ParametroScore']['nivel']  : 0;
		$valor  = isset($classificacao['ParametroScore']['valor'])  ? $classificacao['ParametroScore']['valor']  : 0;
		$pontos = isset($classificacao['ParametroScore']['pontos']) ? $classificacao['ParametroScore']['pontos'] : 0;
		if(!isset($classificacao['ParametroScore']['nivel'])){
			$classificacao['ParametroScore']['nivel']='';
		}
	    if ($classificacao['ParametroScore']['nivel']=='Bronze'){
	    	$pontos_classificacao = 3;
	    }
	    if ($classificacao['ParametroScore']['nivel']=='Latao'){
	    	$pontos_classificacao = 1;
	    }
	    if ($classificacao['ParametroScore']['nivel']=='Cobre'){
	    	$pontos_classificacao = 2;
	    }
	    if ($classificacao['ParametroScore']['nivel']=='Prata'){
	    	$pontos_classificacao = 4;
	    }
	    if ($classificacao['ParametroScore']['nivel']=='Ouro'){
	    	$pontos_classificacao = 5;
	    }

        //debug($pontos);
        //debug($classificacao['ParametroScore']['nivel']);
		if ($retorno['mensagem'] =='PERFIL ADEQUADO AO RISCO' ){ ?>

			<?php 
			//Não mostra Score de alguns profissionais
			$profissional_sem_score = array('Vigilante'=>10,'Prestador de Serviços'=>9,'Funcionario'=>5,'Ajudante'=>6,'Conferente'=>7,'Buonny RH'=>8);

			if(!in_array($this->data['Profissional']['codigo_profissional_tipo'],$profissional_sem_score)) { ?>
 
			
			   <?php if ($valorar !='NAOVALORAR') { ?>		
				        <h2>Classificação do Profissional: </h2> 
               <?php } ?>		
			   <span class="label <?php echo ($valor == 0 ? 'label-important' : 'label-success')?>"><?=$nivel?></span> 
				
				<?php for($i = 1; $i <= 5; $i++): ?>
				<?php   if( $i <= @$pontos_classificacao )
				     		echo $this->Html->image('pneu_dourado.png', array('alt' => ''));
				     	else
				     		echo $this->Html->image('pneu_cinza.png', array('alt' => ''));?>
				<?php endfor; ?>
				<b>(Carga máxima permitida: R$ <?=$this->Buonny->moeda( $valor ) ?>)</b><br/>
			<?php } ?>
		<?php } ?>
			
			
		
	</div>
		
			<br>
			<div style="border:0px solid;width:100%;">			
				
					<?if( $codigo_log_faturamento ) { ?>
					<span style="color:#005D9C;"><font size='4' ><b>Número de Consulta: </b></font></span>
					  <?php if ($retorno['mensagem'] != "PERFIL ADEQUADO AO RISCO" || $classificacao['ParametroScore']['nivel']='') {
						       echo "<b><font size=3>"."XXXXX"."</font></b>"; //+++
	                       }else{
	                           echo "<b><font size=3>".$codigo_log_faturamento."</font></b>";
	                       }
						 ?>					
					
			
		</div><br>
		<?php } ?>
		<?php if ($retorno['mensagem'] == "PERFIL ADEQUADO AO RISCO") { ?>
			<?php if ($this->data['Profissional']['codigo_profissional_tipo']==1) { ?>
		        <div style="border:0px solid;width:100%;">	
		          
		          	<span style="color:#005D9C;"><font size='4'><b>Validade :</b></span>O Embarque</font>
				</div>
	        <?php }else{?>
	             <div style="border:0px solid;width:100%;">	
		          
		          	<span style="color:#005D9C;"><font size='4'><b>Validade :</b></span>Simples Conferência
				</font></div><br>
	        <?php } ?>
	     <?php } ?>
	        	<div style="width:100%;text-align:justify;">
						<!--<span style="font-weight:bold;"><?php //echo $retorno['descricao']; ?></br></span> -->
						<span style="font-weight:bold;"><p><?php echo $retorno['observacao']; ?></p></span>
				</div>
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