<legend>Dados Complementares</legend>
<div id="questionario">
	<?php foreach($questoes as $key=>$questao):?>
		<?php if ($key<3) { ?>
			<?php echo $this->BForm->label($questao['Questao']['descricao'], $questao['Questao']['descricao']); ?>
			<div class="row-fluid inline">
			<?php echo $this->BForm->input("FichaScorecardQuestaoResposta.{$questao['Questao']['codigo']}.codigo_questao_resposta", array('type' => 'radio', 'options' => $questao['Questao']['respostas'], 'default' => 0, 'legend' => false, 'label' => array('class' => 'radio input-medium'))); ?>
			<?php echo $this->BForm->input("FichaScorecardQuestaoResposta.{$questao['Questao']['codigo']}.observacao", array('label'=>false, 'type'=>'text', 'class'=>'input-small  pull-left just-number observacao', 'style'=>'text-align:right')); ?>
			</div>
		<?php } ?>
	<?php endforeach; ?>
</div>
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function(){ setup_questionario(); })");?>