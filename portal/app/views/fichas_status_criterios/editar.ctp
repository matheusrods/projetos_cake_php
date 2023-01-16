<?php echo $this->Buonny->link_css('fichas_scorecard'); ?>

<?php
    if( $success ){
        echo $javascript->codeBlock("
        	window.opener.location.reload();
            window.close();");exit;
    }
?>


<ul class="nav nav-tabs">
    <li class="active"><a id='a_pesquisa' href="#pesquisa" data-toggle="tab">Pesquisa</a></li>
	<li><a id='a_historico' href="#historico" data-toggle="tab">Histórico Ocorrências</a></li>
	<li><a id='a_historicocpf' href="#historicocpf" data-toggle="tab">Histórico Profissional</a></li>
	<li><a id='a_historicorma' href="#historicorma" data-toggle="tab">Histórico RMA</a></li>  
	<li><a id='a_historicosinistro' href="#historicosinistro" data-toggle="tab">Sinistro</a></li> 
	<li><a id='a_historicosocioeco' href="#historicosocioeco" data-toggle="tab">Situação Sócio Economica</a></li>
    <li><a id='a_historicoapontamentos' href="#historicoapontamentos" data-toggle="tab">Histórico Apontamentos</a></li>
	<li><a id='a_ficha' href="#ficha" data-toggle="tab">Dados da Ficha</a></li>
</ul>
<div class="tab-content tabbable">
	<div class="tab-pane active" id="pesquisa" style="width: 100%; height:100%; align: center; overflow-x:hidden; overflow-y:auto"> 
		<?php echo $this->BForm->create('FichaStatusCriterio', array('url' => array('controller' => 'fichas_status_criterios', 'action' => 'editar' , $this->passedArgs[0]))); ?>
        <?php echo $this->BForm->input('botao_selecionado', array('type' => 'hidden')) ?>
        <?php echo $this->BForm->input('ExisteOcorrencia', array('type' => 'hidden','value'=>$dados_parametros['ExisteOcorrencia'])) ?>
        <?php echo $this->BForm->input('ExisteOcorrenciaProf', array('type' => 'hidden','value'=>$dados_parametros['ExisteOcorrenciaProf'])) ?>
		<?php echo $this->element('/fichas_status_criterios/cabecalho_ficha'); ?>
		<?php if(trim($observacao_supervisor)==''){
              unset($observacao_supervisor);
		} ?>
		<?php if(!empty($observacao_supervisor)): ?>
			<div class="alert alert-block alert-info">
				<h5>Observação do supervisor</h5>
				<?php echo $observacao_supervisor; ?>
			</div>
		<?php endif; ?>
		<div id='perguntas'>
			<?php echo $this->element('/fichas_status_criterios/lista_criterios', array('disabled'=>false)); ?>
		</div>
		<br />
		<div class="pre_visualizar_score">
			<?if(!empty($pre_visualizar_score)):?>
			<?endif;?>
			<?=$this->Html->link('Pré visualizar Score', 'javascript:void(0)', array('class'=>'previa_score'));?>
			<div id="pre_score">&nbsp;</div>
		</div>
		<hr />
		<?if( !FichaScorecard::ENVIA_EMAIL_SCORECARD ):?>
		<div class=''>
			<?php echo $this->BForm->input('FichaScorecard.codigo_parametro_score', array('class' => 'span3','label' =>'Classificação do Profissional', 'div'=>'control-group input', 'options'=>$classificacao_tlc, 'empty'=>'Classificação')) ?>
		</div>
		<?endif;?>
		<div class='form-actions'>
			<?php echo $this->BForm->submit('Concluir Pesquisa', array('div' => false, 'id'=>'concluir','class' => 'btn btn-primary')); ?>
			<?php echo $this->BForm->submit('Salvar Pendente', array('div' => false, 'id'=>'pendente','class' => 'btn btn-danger')); ?>			
		</div>
		<div class='dialog' style="display:none">
			<?php echo $this->BForm->input('FichaStatusCriterio.observacaodialog', array('div'=>false, 'maxlength' => 256,'class' => 'input-large', 'placeholder' => false, 'label' =>false , 'type' => 'textarea', 'style' =>'width:97%; height:150px; line-height:20px; overflow:hidden;', 'id' => 'textareaobs')) ?>this->data)
		</div>
		<?php echo $this->BForm->end(); ?>
	</div>
    <div class="tab-pane" id="historicocpf">&nbsp;</div> 
	<div class="tab-pane" id="historicorma">&nbsp;</div> 
	<div class="tab-pane" id="historicosinistro">&nbsp;</div> 
	<div class="tab-pane" id="historicosocioeco">&nbsp;</div> 
	<div class="tab-pane" id="historico">&nbsp;</div> 
	<div class="tab-pane" id="historicoapontamentos" style='min-height:50px'>&nbsp;</div> 
	<div class="tab-pane" id="ficha"><?php echo $this->element('/fichas_scorecard/formulario_ficha'); ?></div>
</div>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('fichas_scorecard')) ?>




<?php echo $this->Javascript->codeBlock("

	$(document).on('click', '#a_historico', function() {
		var div_historico_ocorrencia = jQuery('#historico');
		if (div_historico_ocorrencia.html() == '&nbsp;' ) {
        	bloquearDiv(div_historico_ocorrencia);
			div_historico_ocorrencia.load(baseUrl + '/fichas_scorecard/historico_ocorrencia/{$this->passedArgs[0]}');
		}
	});


	$(document).on('click', '#a_historicoapontamentos', function() {
		var div_apontamento = jQuery('#historicoapontamentos');
		if (div_apontamento.html() == '&nbsp;' ) {
			bloquearDiv(div_apontamento);
			div_apontamento.load(baseUrl + '/fichas_scorecard_art_criminais/listar_por_profissional/{$this->data['Profissional']['codigo_documento']}/{$this->data['FichaScorecard']['codigo']}');
		}
	});

	$(document).on('click', '#a_historicocpf', function() {
        var div_historico_profissional = jQuery('#historicocpf');
        if (div_historico_profissional.html() == '&nbsp;' ) {
	        bloquearDiv(div_historico_profissional);
	        div_historico_profissional.load(baseUrl + '/logs_faturamento/historico_profissional/{$dados_parametros['profissional']}');
        }
	});        
    
    $(document).on('click', '#a_historicosinistro', function() {
        var div_historicosinistro = jQuery('#historicosinistro');
        if (div_historicosinistro.html() == '&nbsp;' ) {
	        bloquearDiv(div_historicosinistro);
	        div_historicosinistro.load(baseUrl + '/sinistros/historico_sinistro/{$dados_parametros['profissional']}');
	    }
    });

	$(document).on('click', '#a_historicosocioeco', function() {
        var div_historicosocioeco = jQuery('#historicosocioeco');
        if (div_historicosocioeco.html() == '&nbsp;' ) {
	        bloquearDiv(div_historicosocioeco);
	        div_historicosocioeco.load(baseUrl + '/fichas_scorecard/historico_socioeco/{$dados_parametros['cod_profissional']}/{$dados_parametros['profissional']}/{$dados_parametros['cod_proprietario_veiculo']}/{$dados_parametros['proprietario_veiculo']}/{$dados_parametros['cod_proprietario_carreta']}/{$dados_parametros['proprietario_bitrem']}/{$dados_parametros['proprietario_bitrem']}');
        }
	});
    
    $(document).on('click', '#a_historicorma', function() {
        var div_historicorma = jQuery('#historicorma');
        if (div_historicorma.html() == '&nbsp;' ) {
	        bloquearDiv(div_historicorma);
	        div_historicorma.load(baseUrl + '/fichas_scorecard/historico_rma/{$dados_parametros['profissional']}/{$dados_parametros['codigo_cliente']}/{$dados_parametros['codigo_embarcador']}/{$dados_parametros['codigo_transportador']}');
        }
    });
	
	
	$('.previa_score').click(function(){
    	bloquearDiv( $('#pre_score') );
		previsualizar_score( ".$this->passedArgs[0]." );
	});

	$(document).ready(function() {
		setup_desabilita_formulario_pesquisa();
		setup_exibir_observacao_criterio();
		setup_sinalizar_criterios_insuficientes();
		sinalizar_criterios_insuficientes();
		setup_sinalizar_criterios_divergentes();
		sinalizar_criterios_divergentes();
        $('#pendente').click(function (){
           $('#FichaStatusCriterioBotaoSelecionado').val('pendente');
		});
		$('#FichaStatusCriterioEditarForm').submit(function() {
               var retorno = true;
               if ($('#FichaStatusCriterioBotaoSelecionado').val() != 'pendente' ){
				   var retorno = true;
					$('#pesquisa select').each(function(){ 
						if ( !$(this).val() && $(this).parent().parent().find('input[id$=\"Opcional\"]').val() == 0 ){
							$(this).parent().addClass('error');
							retorno = false;
						}						
					});	                
				   if(!retorno){
						flashMessage('Responda todos os critérios obrigatórios', 'error');
				   }else{
				 		 if($('#pesquisa div.select.warning').length > 0){
									if(confirm('A ficha será salva como incompleta, pois há critérios obrigatórios definidos como insufiente.')==true){
										retorno = true;
									}else{
										retorno = false;
									}
								}
						 if($('#pesquisa div.select.info').length > 0){
									if(confirm('A ficha será salva como divergente.')==true){
										retorno = true;
									}else{
										retorno = false;
									}
								}
                          if ($('#FichaStatusCriterioExisteOcorrencia').val()=='S') {
                              if (confirm('Há ocorrência com veículo com anomalia. Deseja salvar mesmo assim?')==true){
		                          retorno = true;
		                      }else{
		                          retorno = false;
		                      } 

                         } 	
                         if ($('#FichaStatusCriterioExisteOcorrenciaProf').val()=='S') {
                              if (confirm('Há ocorrência com  Profissional/Proprietário com anomalia. Deseja salvar mesmo assim?')==true){
		                          retorno = true;
		                      }else{
		                          retorno = false;
		                      } 

                         }                          
				  }					
							
				} 	
				return retorno;
		       
		}); 
		$('div#lista_contatos :input').removeAttr('disabled', 'disabled');
		$('div#btn_salvar :input').removeAttr('disabled', 'disabled');
		$('.btn-limpar:button').hide();
		$('#btn-profissional').hide();
		$('#btn-proprietario0').hide();
		$('#btn-proprietario1').hide();
		$('#btn-proprietario2').hide();
		$('.remove-contato-profissional').hide();		
	});
");
?>
 