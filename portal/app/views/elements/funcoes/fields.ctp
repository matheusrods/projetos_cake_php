 <div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['Funcao']['codigo'])? $this->data['Funcao']['codigo'] : '')); ?>
</div>  
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge'));?>
</div>
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'funcoes', 'action' => 'index'), array('class' => 'btn')); ?>
</div>