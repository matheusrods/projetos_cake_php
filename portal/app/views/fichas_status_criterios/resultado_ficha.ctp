<?php echo $this->Buonny->link_css('fichas_scorecard'); ?>
<ul class="nav nav-tabs">
	<li class="active"><a href="#pesquisa" data-toggle="tab">Pesquisa</a></li>
	<li><a href="#historico" data-toggle="tab" class="historico">Histórico Ocorrências</a></li>
	<li><a href="#historicocpf" data-toggle="tab" class="historicocpf">Histórico Profissional</a></li>
	<li><a href="#historicorma" data-toggle="tab" class="historicorma">Histórico RMA</a></li>  
	<li><a href="#historicosinistro" data-toggle="tab" class="historicosinistro">Sinistro</a></li> 
	<li><a href="#historicosocioeco" data-toggle="tab" class="historicosocioeco">Situação Sócio Economica</a></li>
    <li><a href="#historicoapontamentos" data-toggle="tab" class="historicoapontamentos">Histórico Apontamentos</a></li>
	<li><a href="#ficha" data-toggle="tab">Dados da Ficha</a></li>
</ul>
<div class="tab-content">
   <div class="tab-pane active" id="pesquisa">
        <?php echo $this->element('/fichas_status_criterios/cabecalho_ficha', array('readonly'=>true)); ?>
		   <div class="alert alert-block alert-info">
			<h5>Observação do supervisor</h5>
			<?php if(!empty($dados_parametros['observacao_supervisor']) ){
				     echo $dados_parametros['observacao_supervisor'];
		          }else{
                     echo  "NENHUM COMENTÁRIO ADICIONADO A FICHA .";
             } ?>
			</div>
		<div id='perguntas'>
			<?php echo $this->element('/fichas_status_criterios/lista_criterios_respondidos'); ?>
		</div>
		<?php echo $this->element('/fichas_status_criterios/resultado_pontuacao'); ?>
		<br />
		<hr />
		<div id="score_manual">
			<?if( !empty($this->data['FichaScorecard']['codigo_score_manual'])): ?>
			<strong>Classificação Manual do Profissional: </strong>			
			<span class="label <?=(in_array($this->data['FichaScorecard']['codigo_score_manual'], array(ParametroScore::INSUFICIENTE, ParametroScore::DIVERGENTE))  ? 'label-important' : 'label-success')?>"><?=$classificacao_tlc[$this->data['FichaScorecard']['codigo_score_manual']]?></span>
			<?endif;?>
		</div>

		<?php 
			if( !in_array($this->data['FichaScorecard']['codigo_status'], array(FichaScorecardStatus::A_PESQUISAR, FichaScorecardStatus::EM_PESQUISA) ) ) :
				//Caso a pesquisa tenha campos insuficiente sera exibido para o aprovador
				if( isset($campos_insuficientes[0]) && count($campos_insuficientes[0]) > 0 ){?>
					<br />
					<legend>Critérios insuficientes</legend>
				<? foreach( $campos_insuficientes as $key => $criterio ){ ?>
					<?php if( !empty($criterio['Criterio']['descricao'])): ?>
					<p class="label">
						<span class="label"><?php echo $criterio['Criterio']['descricao']?>:</span> 
						<span class=""><?php echo $criterio['StatusCriterio']['descricao']?></span>
					</p>
					<br />
					<?php endif;?>
				<? }
				}?>
			<hr>
			<?php 
				//Caso a pesquisa tenha campos insuficiente sera exibido para o aprovador
				if( isset($campos_divergentes[0]) && count($campos_divergentes[0]) > 0 ){?>
					<br />
					<legend>Critérios Divergentes</legend>
				<? foreach( $campos_divergentes as $key => $criterio_d ){ ?>
					<? if( !empty($criterio_d['Criterio']['descricao'])): ?>
					<p class="label">
						<span class="label"><?=( !empty($criterio_d['Criterio']['descricao'])  ? $criterio_d['Criterio']['descricao'] : NULL)?>:</span> 
						<span class=""><?=( !empty($criterio_d['StatusCriterio']['descricao']) ? $criterio_d['StatusCriterio']['descricao'] : NULL)?></span>
					</p>
					<br />
					<? else: ?>
					<?= $campos_divergentes[0] ?>
					<? endif ?>
				<? }
				}?>
				<hr>
			<? endif ?>
       <?php 
			//Caso a pesquisa tenha campos insuficiente sera exibido para o aprovador
			if( isset($dados_parametros['justificativa_alteracao'])){?>
				<br />
				<legend>Justificativa da alteração da classificação</legend>
					<span class="label"><?=$dados_parametros['justificativa_alteracao']?></span>			
		<?php }?>
		<hr>	
		<br/><br/><br/>
		<div class="form-actions">
			<?php 
				if( !$nova_janela ){
					if( $this->data['FichaScorecard']['codigo_status'] == FichaScorecardStatus::FINALIZADA ){
						echo $html->link('Voltar', array('controller' => 'fichas_scorecard', 'action' => 'index_fichas_finalizadas'), array('class' => 'btn'));
					} else { ?>
						<span>
							<?//php echo $html->link('Abrir Pesquisa', array('controller'=>'fichas_status_criterios','action'=>'editar',$this->data['FichaScorecard']['codigo'], true), array('class' => 'btn btn-primary')); ?>
							<?php echo $html->link('Abrir Pesquisa', 'javascript:void(0)', array( 'onclick' => "analisa_ficha( {$this->data['FichaScorecard']['codigo']} )" ,'class' => 'btn btn-primary'));?>
						</span>
						<span><?php echo $html->link('Voltar', array('controller' => 'fichas_scorecard', 'action' => 'fichas_a_pesquisar'), array('class' => 'btn'));?></span>
					<?}
				} else {
					echo $html->link('Voltar', 'javascript:void(0)', array( 'onclick' => "window.close()",'class' => 'btn' ));
				}
			?>
		</div>		
   </div>
   <div class="tab-pane" id="historicocpf">&nbsp;</div>
	<div class="tab-pane" id="historicorma">&nbsp;</div>	
	<div class="tab-pane" id="historicosinistro">&nbsp;</div> 
	<div class="tab-pane" id="historicosocioeco">&nbsp;</div>	
	<div class="tab-pane" id="historico">&nbsp;</div> 
    <div class="tab-pane" id="historicoapontamentos" style='min-height:50px'>&nbsp;</div>
    <div class="tab-pane" id="ficha">
		<?php echo $this->element('/fichas_scorecard/formulario_ficha'); ?>		
	</div>
</div>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('fichas_scorecard')) ?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function(){

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
				//div_historico_ocorrencia.load(baseUrl + '/fichas_scorecard/historico_ocorrencia/{$dados_parametros['profissional']}/{$dados_parametros['veiculo']}/{$dados_parametros['carreta']}/{$dados_parametros['bitrem']}/{$dados_parametros['proprietario_veiculo']}/{$dados_parametros['proprietario_carreta']}/{$dados_parametros['proprietario_bitrem']}');
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
        
		setup_desabilita_formulario();
		setup_exibir_observacao_criterio();	
		setup_sinalizar_criterios_insuficientes();
		setup_codigo_cliente();
		setup_produto();
	});", false);?>

	<?php echo $this->Javascript->codeBlock("
	function analisa_ficha(codigo ){
		url= baseUrl + 'fichas_status_criterios/editar/'+codigo;
		var janela = window_sizes();
		window.open(url, '_blank', 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
		bloquearDiv(jQuery('.container'));
		history.go(-1);
	}
", false);?>