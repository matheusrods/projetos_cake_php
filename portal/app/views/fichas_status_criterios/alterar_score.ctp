<?php echo $this->element('/fichas_status_criterios/cabecalho_ficha', array('readonly'=>true)); ?>
<?php echo $this->BForm->create('FichaStatusCriterio', array('url' => array('controller' => 'fichas_status_criterios', 'action' => 'alterar_score' , $this->passedArgs[0]))); ?>
<div id='perguntas'><?php echo $this->element('/fichas_status_criterios/lista_criterios', array('disabled'=>false)); ?></div>
<?php echo $this->element('/fichas_status_criterios/resultado_pontuacao'); ?>
<hr />
<div class=''>
	<?php echo $this->BForm->input('FichaScorecard.codigo', array('type' => 'hidden', 'value'=>$codigo_ficha)) ?>
	<?if( !FichaScorecard::ENVIA_EMAIL_SCORECARD ):?>
	<div class=''>
		<?php echo $this->BForm->input('FichaScorecard.codigo_parametro_score', array('class' => 'span3','label' =>'Classificação do Profissional', 'div'=>'control-group input', 'options'=>$classificacao_tlc, 'value'=> $score_checked)) ?>
	</div>
	<?else:?>
		<?php echo $this->BForm->input('FichaScorecard.codigo_parametro_score', array('class' => 'span2','label' =>'Nova classificação', 'div'=>'control-group input', 'options'=>$classificacao, 'empty'=>'Classificação', 'value'=>$pontuacao['codigo_parametro_score'])) ?>
	<?endif;?>
	<?php echo $this->BForm->input('FichaScorecard.justificativa_alteracao', array('class' => 'span11','label' =>'Justificativa da alteração da classificação', 'type' => 'textarea', 'div'=>'control-group input textarea observacao')) ?>
</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary', 'name'=>'aprovar')); ?>
	<?=$html->link('Voltar', array('controller' => 'fichas_scorecard', 'action' => 'index_fichas_finalizadas'), array('class' => 'btn','id'=>'button')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('fichas_scorecard')) ?>
<?php echo $this->Javascript->codeBlock("$(document).ready(function() {setup_exibir_observacao_criterio();});");?>