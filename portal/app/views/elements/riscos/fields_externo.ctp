 <div class='row-fluid inline'>
	<?php echo $this->BForm->input('Risco.nome_agente', array('label' => 'Risco', 'readonly' => true, 'class' => 'input-xxlarge')); ?>
	<?php echo $this->BForm->input('Risco.ativo', array('label' => 'Status', 'readonly' => true, 'class' => 'input', 'default' => '', 'options' => ( $this->data['Risco']['ativo'] ? array(1 => 'Ativo') : array(0 => 'Inativo') ) ) ); ?>
	<?php echo $this->BForm->input('RiscoExterno.codigo_externo', array('label' => 'Código Externo (*)', 'class' => 'input-large' )); ?>
  	<?php echo $this->BForm->hidden('codigo'); ?>
  	<?php echo $this->BForm->hidden('codigo_riscos',array('value'=>$this->data['Risco']['codigo'])); ?>
  	<?php echo $this->BForm->hidden('codigo_cliente',array('value'=>$this->params['pass'][0])); ?>
  </div>  
  <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'riscos', 'action' => 'index_externo'), array('class' => 'btn')); ?>
</div>