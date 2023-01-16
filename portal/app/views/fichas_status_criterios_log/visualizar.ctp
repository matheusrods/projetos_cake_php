<?php echo $this->Buonny->link_css('fichas_scorecard'); ?>
<ul class="nav nav-tabs">
	<li class="active"><a href="#pesquisa" data-toggle="tab">Pesquisa</a></li>
	<li><a href="#ficha" data-toggle="tab">Dados da Ficha</a></li>
</ul>
<div class="tab-content">
   <div class="tab-pane active" id="pesquisa">
        <?php echo $this->element('/fichas_status_criterios/cabecalho_ficha', array('readonly'=>true)); ?>
		   <div class="alert alert-block alert-info">
			<h5>Observação do supervisor</h5>
			<?php if(!empty($this->data['FichaScorecard']['observacao_supervisor']) ){
				     echo $this->data['FichaScorecard']['observacao_supervisor'];
		          }else{
                     echo  "NENHUM COMENTÁRIO ADICIONADO A FICHA.";
             } ?>
			</div>
		<div id='perguntas'>
			<?php echo $this->element('/fichas_status_criterios/lista_criterios_respondidos'); ?>
		</div>
		<?php echo $this->element('/fichas_status_criterios/resultado_pontuacao'); ?>
		<br />
		<hr />
		<div id="score_manual">
			<?php if( (FichaScorecard::ENVIA_EMAIL_SCORECARD === FALSE) && !empty($this->data['FichaScorecard']['codigo_score_manual']) ) : ?>
			<strong>Classificação Manual do Profissional: </strong>			
			<span class="label <?=(in_array($this->data['FichaScorecard']['codigo_score_manual'], array(ParametroScore::INSUFICIENTE, ParametroScore::DIVERGENTE))  ? 'label-important' : 'label-success')?>">
			<?=$classificacao_tlc[$this->data['FichaScorecard']['codigo_score_manual']]?>
			</span>
			<?php endif;?>
		</div>
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
			<hr>
       <?php 
			//Caso a pesquisa tenha campos insuficiente sera exibido para o aprovador
			if( isset($this->data['FichaScorecard']['justificativa_alteracao'])){?>
				<br />
				<legend>Justificativa da alteração da classificação</legend>
					<span class="label"><?php echo $this->data['FichaScorecard']['justificativa_alteracao'];?></span>			
		<?php }?>
		<hr>	
		<br/><br/><br/>
		<?= $html->link('Voltar', array('controller' => 'fichas_scorecard', 'action' => 'fichas_log'), array('class' => 'btn','id'=>'button')); ?>
   </div>
    <div class="tab-pane" id="ficha"><?php echo $this->element('/fichas_scorecard/formulario_ficha'); ?></div>
</div>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('fichas_scorecard')) ?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function(){
		setup_desabilita_formulario();
		setup_exibir_observacao_criterio();	
		setup_sinalizar_criterios_insuficientes();
		setup_codigo_cliente();
		setup_produto();
	});", false);?>