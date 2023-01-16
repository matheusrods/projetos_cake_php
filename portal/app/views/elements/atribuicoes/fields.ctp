 <div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['Atribuicao']['codigo'])? $this->data['Atribuicao']['codigo'] : '')); ?>
 	<?php echo $this->BForm->hidden('codigo_cliente', array('value' =>  !empty($this->data['Atribuicao']['codigo_cliente']) ? $this->data['Atribuicao']['codigo_cliente'] : '')); ?>
</div>  
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge'));?>
	<?php echo $this->BForm->input('codigo_externo', array('label' => 'Codigo Externo', 'class' => 'input-large'));?>
</div>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'atribuicoes', 'action' => 'gerenciar', $this->data['Atribuicao']['codigo_cliente'] ), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
	setup_mascaras(); 
	setup_datepicker();
});
'); ?>