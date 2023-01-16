 <div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['TipoGlosas']['codigo'])? $this->data['TipoGlosas']['codigo'] : '')); ?>
	
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xlarge'));?>
	<?php echo $this->BForm->input('visualizacao_do_cliente', array('label' => 'Visualização do Cliente (*)', 'class' => 'input-xlarge'));?>
</div>
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'tipo_glosas', 'action' => 'index'), array('class' => 'btn')); ?>
</div>