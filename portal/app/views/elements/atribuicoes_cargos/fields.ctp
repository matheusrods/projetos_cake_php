 <div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['AtribuicaoCargo']['codigo'])? $this->data['AtribuicaoCargo']['codigo'] : '')); ?>
 	<?php //echo $this->BForm->hidden('codigo_cliente', array('value' =>  !empty($this->data['AtribuicaoCargo']['codigo_cliente']) ? $this->data['AtribuicaoCargo']['codigo_cliente'] : '')); ?>
 	<?php echo $this->BForm->hidden('ativo', array('value' =>  1)); ?>
</div>  
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge'));?>
</div>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'atribuicoes_cargos', 'action' => 'gerenciar' ), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
	setup_mascaras(); 
	setup_datepicker();
});
'); ?>