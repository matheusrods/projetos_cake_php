<?php echo $this->Buonny->link_css('fichas_scorecard'); ?>
<?php 
      if(!isset($this->params['pass'][1])):
      	$this->params['pass'][1]='';
      endif;
      if ($this->params['pass'][1]==1) :         
         $tab_pane = "active";
         $tab_pane_pesquisa = "";
      else:      	 
      	 $tab_pane_pesquisa = "active";
      	 $tab_pane = "";
      endif;
?>

<?php
    if( $success ){
        echo $javascript->codeBlock("
        	window.opener.location.reload();
            window.close();");exit;    
    }
?>


<ul class="nav nav-tabs">
	<li class="active"><a href="#perguntas" data-toggle="tab">Pesquisa</a></li>
	<li><a href="#historico" data-toggle="tab" class="historico">Histórico Ocorrências</a></li>
	<li><a href="#historicocpf" data-toggle="tab" class="historicocpf">Histórico Profissional</a></li>
	<li><a href="#historicorma" data-toggle="tab" class="historicorma">Histórico RMA</a></li>  
	<li><a href="#historicosinistro" data-toggle="tab" class="historicosinistro">Sinistro</a></li> 
	<li><a href="#historicosocioeco" data-toggle="tab" class="historicosocioeco">Situação Sócio Economica</a></li> 
	<?php if ($tab_pane=='active') : ?>
        <li class="active"><a href="#historicoapontamentos" data-toggle="tab" class="historicoapontamentos">Histórico Apontamentos</a></li>
    <? else : ?>
        <li><a href="#historicoapontamentos" data-toggle="tab" class="historicoapontamentos">Histórico Apontamentos</a></li>
    <?php endif; ?>
	<li><a href="#ficha" data-toggle="tab">Dados da Ficha</a></li>
</ul>
<div class="tab-content">	
	<div class="tab-pane active" id='perguntas' style="width: 100%; height:100%; align: center; overflow-x:hidden; overflow-y:auto">
		<?php echo $this->BForm->create('FichaStatusCriterio', array('url' => array('controller' => 'fichas_status_criterios', 'action' => 'aprovar', $this->passedArgs[0]))); ?>
		<?php echo $this->BForm->input('BotaoClicado', array('type' => 'hidden')) ?>
		<?php echo $this->element('/fichas_status_criterios/cabecalho_ficha', array('readonly'=>false)); ?>
		    <div class="alert alert-block alert-info">
				<h5>Observação do supervisor</h5>
				<?php if(!empty($dados_parametros['observacao_supervisor']) ){ 
					     echo "<br><br><br>".$dados_parametros['observacao_supervisor']; 
			          }else{ 
	                     echo  "<br><br><br>"."NENHUM COMENTÁRIO ADICIONADO A FICHA ."; 
	             } ?>				
			</div>
		<div class="lst_criterios">
			<?php echo $this->element('/fichas_status_criterios/lista_criterios', array('disabled'=>false)); ?>
		</div>
		<?if( !FichaScorecard::ENVIA_EMAIL_SCORECARD ):?>
		<div class=''>
			<?php echo $this->BForm->input('FichaScorecard.codigo_parametro_score', array('class' => 'span3','label' =>'Classificação do Profissional', 'div'=>'control-group input', 'options'=>$classificacao_tlc, 'value'=> $score_checked)) ?>
			<?php echo $this->BForm->input('FichaScorecard.justificativa_alteracao', array('class' => 'span11','label' =>'Justificativa da alteração da classificação', 'value'=>@$justificativa ,'type' => 'textarea', 'div'=>'control-group input textarea justificativa')) ?>
		</div>
		<?endif;?>

		<div class='exibe_resultado_pontuacao'>
			<?//if( FichaScorecard::ENVIA_EMAIL_SCORECARD ):?>
			<?php echo $this->element('/fichas_status_criterios/resultado_pontuacao'); ?>
			<?//endif;?>
			<?php 
			//Caso a pesquisa tenha campos insuficiente sera exibido para o aprovador
			if( isset($campos_insuficientes[0]) && count($campos_insuficientes[0]) > 0 ){?>
				<br />
				<legend>Critérios insuficientes</legend>
			<? foreach( $campos_insuficientes as $key => $criterio ){ ?>
				<p class="label">
					<span class="label"><?=$criterio['Criterio']['descricao']?>:</span> 
					<span class=""><?=$criterio['StatusCriterio']['descricao']?></span>
				</p>
				<br />
			<? }
			}?>
			<hr>
			<?php 
			//Caso a pesquisa tenha campos insuficiente sera exibido para o aprovador
			if( isset($campos_divergentes[0]) && count($campos_divergentes[0]) > 0 ){?>
				<br />
				<legend>Critérios Divergentes</legend>
			<? foreach( $campos_divergentes as $key => $criterio_d ){ ?>
				<p class="label">
					<span class="label"><?=$criterio_d['Criterio']['descricao']?>:</span> 
					<span class=""><?=$criterio_d['StatusCriterio']['descricao']?></span>
				</p>
				<br />
			<? }
			}?>
			<?if( $alteracao_manual ):?>
				<!-- <span class="label">A CLASSIFICAÇÃO DO PROFISSIONAL FOI ALTERADA MANUALMENTE.</span> -->
			<?endif;?>			
			<div class=''> 
				<?php echo $this->BForm->input('FichaStatusCriterio.observacao_supervisor', array('class' => 'span11','label' =>'Observação para o operador','value'=>$dados_parametros['observacao_supervisor'] ,'type' => 'textarea', 'div'=>'control-group input textarea observacao')) ?>
			</div>
			<div class='form-actions'>
				<?php echo $this->BForm->submit('Finalizar', array('finalizar','id'=>'finalizar','div' => false, 'class' => 'btn btn-primary', 'name'=>'aprovar')); ?>
				<?php echo $this->BForm->submit('Salvar Pendente', array('id'=>'pendente','div' => false, 'class' => 'btn', 'name'=>'pendente')); ?>
				<?php echo $this->BForm->submit('Devolver', array('id'=>'devolver', 'div' => false, 'class' => 'btn btn-danger', 'name'=>'reprovar')); ?>
				<?php echo $this->BForm->hidden('FichaScorecard.codigo_usuario_responsavel');?>
			</div>
		</div>

		<div class='exibe_recalcular_score'>
			<div class='form-actions'>				
				<?php echo $this->BForm->submit('Finalizar', array('div' => false, 'class' => 'btn btn-primary', 'name'=>'recalcular')); ?>
				<?=$html->link('Voltar', array('controller' => 'fichas_scorecard', 'action' => 'fichas_a_aprovar'), array('class' => 'btn','id'=>'button')); ?>
			</div>
		</div>
		<?php echo $this->BForm->end(); ?>
	</div>
	<div class="tab-pane" id="historicocpf">&nbsp;</div> 
	<div class="tab-pane" id="historicorma">&nbsp;</div>	
	<div class="tab-pane" id="historicosinistro">&nbsp;</div> 
	<div class="tab-pane" id="historicosocioeco">&nbsp;</div>	
	<div class="tab-pane" id="historico">&nbsp;</div> 
	<?php if ($tab_pane=='active') : ?>
	   <div class="tab-pane active" id="historicoapontamentos" style='min-height:50px'>&nbsp;</div> 
	<?php else : ?>
       <div class="tab-pane" id="historicoapontamentos" style='min-height:50px'>&nbsp;</div> 
    <?php endif; ?>
	<div class="tab-pane" id="ficha"><?php echo $this->element('/fichas_scorecard/formulario_ficha');?></div>
</div>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('fichas_scorecard')) ?>
<?php echo $this->Javascript->codeBlock("
	$(document).ready(function() {
		var codigo_parametro_score = {$pontuacao['codigo_parametro_score']};
		$('#avancar').hide();

		$('.historicoapontamentos').click(function(){
			if( $('#historicoapontamentos').html() == '&nbsp;' ){
				var div_apontamento = jQuery('#historicoapontamentos');
				bloquearDiv(div_apontamento);
				div_apontamento.load(baseUrl + '/fichas_scorecard_art_criminais/listar_por_profissional/{$this->data['Profissional']['codigo_documento']}/{$this->data['FichaScorecard']['codigo']}');
			}
		});

		$('.historico').click(function(){
			if( $('#historico').html() == '&nbsp;' ){
				var div_historico_ocorrencia = jQuery('#historico');
				bloquearDiv(div_historico_ocorrencia);
				div_historico_ocorrencia.load(baseUrl + '/fichas_scorecard/historico_ocorrencia/{$this->passedArgs[0]}');
			}
		});

		$('.historicocpf').click(function(){
			if( $('#historicocpf').html() == '&nbsp;' ){
				var div_historico_profissional = jQuery('#historicocpf');
				bloquearDiv(div_historico_profissional);
				div_historico_profissional.load(baseUrl + '/logs_faturamento/historico_profissional/{$dados_parametros['profissional']}');
			}
		});
        
		$('.historicorma').click(function(){
			if( $('#historicorma').html() == '&nbsp;' ){
				var div_historicorma = jQuery('#historicorma');
				bloquearDiv(div_historicorma);
				div_historicorma.load(baseUrl + '/fichas_scorecard/historico_rma/{$dados_parametros['profissional']}/{$dados_parametros['codigo_cliente']}/{$dados_parametros['codigo_embarcador']}/{$dados_parametros['codigo_transportador']}');
			}
		});

		$('.historicosinistro').click(function(){
			if( $('#historicosinistro').html() == '&nbsp;' ){
				var div_historicosinistro = jQuery('#historicosinistro');
				bloquearDiv(div_historicosinistro);
				div_historicosinistro.load(baseUrl + '/sinistros/historico_sinistro/{$dados_parametros['profissional']}');
			}
		});

		$('.historicosocioeco').click(function(){
			if( $('#historicosocioeco').html() == '&nbsp;' ){
				var div_historicosocioeco = jQuery('#historicosocioeco');
				bloquearDiv(div_historicosocioeco);
				div_historicosocioeco.load(baseUrl + '/fichas_scorecard/historico_socioeco/{$dados_parametros['cod_profissional']}/{$dados_parametros['profissional']}/{$dados_parametros['cod_proprietario_veiculo']}/{$dados_parametros['proprietario_veiculo']}/{$dados_parametros['cod_proprietario_carreta']}/{$dados_parametros['proprietario_bitrem']}/{$dados_parametros['proprietario_bitrem']}');
			}
		});
    
		$('.exibe_recalcular_score').hide();
		$('.justificativa').hide();
		setup_exibir_observacao_criterio();	
		setup_sinalizar_criterios_insuficientes();
		sinalizar_criterios_insuficientes();
		$('#FichaScorecardCodigoParametroScore').change(function() {
			
			console.log( $(this).val() );
			console.log( codigo_parametro_score );


			if( $(this).val() && $(this).val() != codigo_parametro_score ){
	    		$('.exibe_resultado_pontuacao').hide();
	    		$('.exibe_recalcular_score').show();
	    		$('.justificativa').show();
			} else {
	    		$('.exibe_resultado_pontuacao').show();
	    		$('.exibe_recalcular_score').hide();
	    		$('.justificativa').hide();
			}
		});		

		$('#FichaStatusCriterioEditarForm').submit(function() {
			var retorno = true;	
			$('#pesquisa select').each(function(){ 
				if (!$(this).val() && $(this).parent().parent().find('input[id$=\"Opcional\"]').val() == 0){
					$(this).parent().addClass('error');
					retorno = false;
				}
			}); 
			if(!retorno){
				flashMessage('Responda todos os critérios obrigatórios', 'error');
			}else{
				if($('#pesquisa div.select.warning').length > 0){
					alert('A ficha será salva como incompleta, pois há critérios obrigatórios definidos como insufiente.')
				}
			}
			return retorno;
		});
		$('div#lista_contatos :input').removeAttr('disabled', 'disabled');
		$('div#btn_salvar :input').removeAttr('disabled', 'disabled');
		$('div#dados_profissional :input').attr('readonly', true);
        $('#pendente').click(function (){
           $('#FichaStatusCriterioBotaoClicado').val('pendente');		    
		}); 
        $('#devolver').click(function (){
           $('#FichaStatusCriterioBotaoClicado').val('devolver');		    
		}); 
        $('#finalizar').click(function (){
           $('#FichaStatusCriterioBotaoClicado').val('finalizar');		    
		}); 

	});
");
?>