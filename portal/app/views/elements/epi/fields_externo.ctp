 <div class='row-fluid inline'>
	<?php echo $this->BForm->input('Epi.nome', array('label' => 'Descrição', 'readonly' => true, 'class' => 'input-xxlarge')); ?>
	<?php echo $this->BForm->input('Epi.ativo', array('label' => 'Status', 'readonly' => true, 'class' => 'input', 'default' => '', 'options' => ( $this->data['Epi']['ativo'] ? array(1 => 'Ativo') : array(0 => 'Inativo') ) ) ); ?>
	<?php echo $this->BForm->input('EpiExterno.codigo_externo', array('label' => 'Código Externo (*)', 'class' => 'input-large' )); ?>
  	<?php echo $this->BForm->hidden('codigo'); ?>
  	<?php echo $this->BForm->hidden('codigo_epi',array('value'=>$this->data['Epi']['codigo'])); ?>
  	<?php echo $this->BForm->hidden('codigo_cliente',array('value'=>$this->params['pass'][0])); ?>
  </div>  
  <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'epi', 'action' => 'index_externo'), array('class' => 'btn')); ?>
</div>