 <div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['TipoServicosNfs']['codigo'])? $this->data['TipoServicosNfs']['codigo'] : '')); ?>
	
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xlarge'));?>
</div>
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'tipo_servicos_nfs', 'action' => 'index'), array('class' => 'btn')); ?>
</div>