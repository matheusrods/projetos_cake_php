 <div class='row-fluid'>
	 <div class='row-fluid inline'>
	 	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['ExameFuncao']['codigo'])? $this->data['ExameFuncao']['codigo'] : '')); ?>	 	
		<?php echo $this->BForm->input('codigo_exame', array('label' => 'Exame (*)', 'class' => 'input-xxlarge', 'default' => '', 'empty' => 'Selecione', 'options' => $exames)); ?>
	</div>

	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('codigo_funcao', array('label' => 'Função (*)', 'class' => 'input-xxlarge',  'default' => '', 'empty' => 'Selecione', 'options' => $funcoes)); ?>
	</div>

<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'exames_funcoes', 'action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
	setup_mascaras(); 
	setup_datepicker();
});
'); ?>