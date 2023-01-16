 <div class="well">
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->hidden('codigo');?>
		<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge')); ?>
	</div>
</div>

<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>