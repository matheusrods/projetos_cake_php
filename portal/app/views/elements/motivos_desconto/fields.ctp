 <div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['MotivosDesconto']['codigo'])? $this->data['MotivosDesconto']['codigo'] : '')); ?>
	
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xlarge'));?>
</div>
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'motivos_desconto', 'action' => 'index'), array('class' => 'btn')); ?>
</div>