 <div class='row-fluid'>
	 <div class='row-fluid inline'>
	 	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['RiscoExame']['codigo'])? $this->data['RiscoExame']['codigo'] : '')); ?>	 	
	 	<?php echo $this->BForm->hidden('codigo_cliente', array('value' =>  !empty($this->data['RiscoExame']['codigo_cliente'])? $this->data['RiscoExame']['codigo_cliente'] : '')); ?>
		<?php echo $this->BForm->input('codigo_risco', array('label' => 'Nome do Agente (*)', 'class' => 'input-xxlarge',  'default' => '', 'empty' => 'Selecione', 'options' => $riscos)); ?>
	</div>

	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('codigo_exame', array('label' => 'Exame (*)', 'class' => 'input-xxlarge', 'default' => '', 'empty' => 'Selecione', 'options' => $exames)); ?>
	</div>

<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'riscos_exames', 'action' => 'gerenciar', $this->data['RiscoExame']['codigo_cliente'] ), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
	setup_mascaras(); 
	setup_datepicker();
});
'); ?>
