<?php echo $this->BForm->create('Questao', array('url' => array('controller' => 'questoes', 'action' => 'alterar', $codigo_questionario, $codigo))) ?>
<div class='well'>
	<div class="row-fluid inline">
		<?php if(isset($this->data['Questao']['codigo'])) { echo $this->BForm->input('codigo'); } ?>

		<?php if($resposta) { ?>
		<?php echo $this->BForm->hidden('codigo_questao', array('value' => $this->data['Questao']['codigo_questao'])) ?>
		<?php } else { ?>
		<?php echo $this->BForm->hidden('codigo_resposta', array('value' => $this->data['Questao']['codigo_questao_resposta'])) ?>
		<?php } ?>
		
		<?php if(!empty($this->data['LabelQuestao']['codigo'])) { ?>
		<?php echo $this->BForm->hidden('codigo_label_questao', array('value' => $this->data['LabelQuestao']['codigo'], 'id' => 'QuestaoCodigoLabelQuestao')) ?>

		<?php } ?>

		<?php if($resposta) { ?>
		<?php echo $this->BForm->input('LabelQuestao.label', array('class' => 'input-xxlarge', 'id' => 'QuestaoLabel', 'label' => $label, 'div' => 'input-append', 'after' => $this->BForm->button('Respostas salvas', array('type' => 'button', 'class' => 'btn btn-default', 'id' => 'LabelQuestaoCodigo', 'data-type' => 'R')))) ?>
		<div class="clear"></div>
		<?php echo $this->BForm->input('pontos', array('class' => 'input-small', 'label' => 'Pontos')) ?>
		<div class="clear"></div>

		<?php echo $this->BForm->input('codigo_proxima_questao', array('options' => $questoes, 'empty' => 'Selecione', 'class' => 'input-xxlarge', 'label' => 'A qual questão esta resposta deverá direcionar?')) ?>
		<div class="clear"></div>

		<?php } else { ?>

		<?php echo $this->BForm->input('LabelQuestao.label', array('class' => 'input-xxlarge', 'id' => 'QuestaoLabel', 'label' => $label, 'div' => ((!$resposta)? 'input-append' : ''), 'after' => ((!$resposta)? $this->BForm->button('Escolher pergunta pronta', array('type' => 'button', 'class' => 'btn btn-default', 'id' => 'LabelQuestaoCodigo', 'data-type' => 'Q')) : '' ))) ?>
		<div class="clear"></div>
		<?php } ?>

		<?php // echo $this->BForm->input('status', array('class' => 'input-small', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))) ?>
		<!-- <div class="clear"></div> -->

		<?php echo $this->BForm->input('observacoes', array('class' => 'input-xxlarge', 'label' => 'Observações', 'rows' => 2)) ?>
		<div class="clear"></div>
	</div>
</div>	
<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?> &nbsp;
	<?php echo $html->link('Voltar', array('action' => 'index', $codigo_questionario), array('class' => 'btn')); ?>
</div>

<?php echo $this->Buonny->link_js('search'); ?>
<?php echo $this->Javascript->codeBlock("
	$(document).ready(function() {
		$('#LabelQuestaoCodigo').search_label_questoes();
		$('#QuestaoLabel').keyup(function(event) {
			$('#QuestaoCodigoLabelQuestao').remove();
		});
	});
	", false); ?>