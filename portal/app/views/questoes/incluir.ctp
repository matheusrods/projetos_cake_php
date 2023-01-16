<?php echo $this->BForm->create('Questao', array('url' => array('controller' => 'questoes', 'action' => 'incluir', $metodo, $codigo_questionario, $codigo_questao_resposta))) ?>
<div class='well'>
	<div class="row-fluid inline">
		<?php if(isset($this->data['Questao']['codigo'])) { echo $this->BForm->input('codigo'); } ?>

		<?php if($resposta) { ?>
		<?php echo $this->BForm->hidden('codigo_questao', array('value' => $codigo_questao_resposta)) ?>
		<?php } else { ?>
		<?php echo $this->BForm->hidden('codigo_resposta', array('value' => $codigo_questao_resposta)) ?>
		<?php } ?>

		<?php echo $this->BForm->hidden('codigo_questionario', array('value' => $codigo_questionario)) ?>
		
		<?php if($resposta && empty($codigo_questao_resposta)) { ?>
		<?php echo $this->BForm->input('codigo_questao', array('options' => $questoes, 'label' => 'Selecione uma questão pai', 'empty' => 'Selecione')) ?>
		<div class="clear"></div>
		<?php } ?>


		<?php if($resposta) { ?>
		<?php echo $this->BForm->input('label', array('class' => 'input-xxlarge', 'label' => $label, 'div' => 'input-append', 'after' => $this->BForm->button('Respostas salvas', array('type' => 'button', 'class' => 'btn btn-default', 'id' => 'LabelQuestaoCodigo', 'data-type' => 'R')))) ?>
		<div class="clear"></div>
		<?php echo $this->BForm->input('pontos', array('class' => 'input-small', 'label' => 'Pontos')) ?>
		<div class="clear"></div>

		<?php } else { ?>

		<?php echo $this->BForm->input('label', array('class' => 'input-xxlarge', 'label' => $label, 'div' => ((!$resposta)? 'input-append' : ''), 'after' => ((!$resposta)? $this->BForm->button('Escolher pergunta pronta', array('type' => 'button', 'class' => 'btn btn-default', 'id' => 'LabelQuestaoCodigo', 'data-type' => 'Q')) : '' ))) ?>
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