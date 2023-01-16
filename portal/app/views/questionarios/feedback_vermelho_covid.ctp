<?php echo $this->BForm->create('ClienteQuestionarios', array('type' => 'file', 'enctype' => 'multipart/form-data','url' => array('controller' => 'Questionarios', 'action' => 'feedback_vermelho_covid',$dados['ClienteQuestionarios']['codigo_cliente'],$dados['ClienteQuestionarios']['codigo_questionario']), 'divupdate' => '.form-procurar')); ?>
<div class='well'>


	<div class="row-fluid inline">
		<?php if(isset($dados['ClienteQuestionarios']['codigo'])) { 
			$codigo = $dados['ClienteQuestionarios']['codigo'];
			echo $this->BForm->hidden('codigo', array('value'=>$codigo)); 
		} ?>
		<?php
			$descricao = (isset($dados['ClienteQuestionarios']['feedback_vermelho_covid'])) ?  $dados['ClienteQuestionarios']['feedback_vermelho_covid'] : '';
			echo $this->BForm->input('feedback_vermelho_covid', array('class' => 'input-xxlarge', 'label' => 'Feedback', 'value'=> $descricao )) 
		?>	
	</div>


</div>	
<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?> &nbsp;
	<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end() ?>
