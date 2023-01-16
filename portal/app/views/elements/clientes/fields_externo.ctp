 <div class='row-fluid inline'>
	<?php echo $this->BForm->input('Cliente.razao_social', array('label' => 'Razão Social', 'readonly' => true, 'class' => 'input-xxlarge')); ?>
	<?php echo $this->BForm->input('Cliente.ativo', array('label' => 'Status', 'readonly' => true, 'class' => 'input', 'default' => '', 'options' => ( $this->data['Cliente']['ativo'] ? array(1 => 'Ativo') : array(0 => 'Inativo') ) ) ); ?>
	<?php echo $this->BForm->input('ClienteExterno.codigo_externo', array('label' => 'Código Externo (*)', 'class' => 'input-large' )); ?>
  	<?php echo $this->BForm->hidden('codigo'); ?>
  	<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $this->data['Cliente']['codigo'])); ?>
  	<?php //echo $this->BForm->hidden('codigo_cliente', array('value' => $this->params['pass'][0])); ?>
  </div>  
  
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'clientes', 'action' => 'index_externo'), array('class' => 'btn')); ?>
</div>