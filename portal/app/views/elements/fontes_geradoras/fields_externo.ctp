 <div class='row-fluid inline'>
	<?php echo $this->BForm->input('FonteGeradora.nome', array('label' => 'Descrição', 'readonly' => true, 'class' => 'input-xxlarge')); ?>
	<?php echo $this->BForm->input('FonteGeradora.ativo', array('label' => 'Status', 'readonly' => true, 'class' => 'input', 'default' => '', 'options' => ( $this->data['FonteGeradora']['ativo'] ? array(1 => 'Ativo') : array(0 => 'Inativo') ) ) ); ?>
	<?php echo $this->BForm->input('FonteGeradoraExterno.codigo_externo', array('label' => 'Código Externo (*)', 'class' => 'input-large' )); ?>
  	<?php echo $this->BForm->hidden('codigo'); ?>
  	<?php echo $this->BForm->hidden('codigo_fontes_geradoras', array('value' => $this->data['FonteGeradora']['codigo'])); ?>
  	<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $this->params['pass'][0])); ?>
  </div>  
  
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'fontes_geradoras', 'action' => 'index_externo'), array('class' => 'btn')); ?>
</div>