 <div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['Cid']['codigo'])? $this->data['Cid']['codigo'] : '')); ?>
	<?php echo $this->BForm->input('codigo_cid10', array('label' => 'CID10 (*)', 'type' => 'text', 'class' => 'input-mini' )); ?>
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xlarge'));?>
</div>
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'cid', 'action' => 'index'), array('class' => 'btn')); ?>
</div>