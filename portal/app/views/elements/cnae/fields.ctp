 <div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['Cnae']['codigo'])? $this->data['Cnae']['codigo'] : '')); ?>
	<?php echo $this->BForm->input('cnae', array('label' => 'CNAE (*)', 'type' => 'text', 'class' => 'input-mini' )); ?>
	<?php echo $this->BForm->input('secao', array('label' => 'Seção (*)', 'class' => 'input-small', 'empty' => 'Selecione','options' => $secao)); ?>
	<?php echo $this->BForm->input('grau_risco', array('label' => 'Grau de Risco (*)', 'class' => 'input-mini')); ?>
</div>  
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição', 'class' => 'input-xxlarge'));?>
</div>
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'cnae', 'action' => 'index'), array('class' => 'btn')); ?>
</div>