 <div class='row-fluid inline'>
	<?php echo $this->BForm->input('GrupoRisco.descricao', array('label' => 'Descrição', 'readonly' => true, 'class' => 'input-xxlarge')); ?>
	<?php echo $this->BForm->input('GrupoRisco.ativo', array('label' => 'Status', 'readonly' => true, 'class' => 'input', 'default' => '', 'options' => ( $this->data['GrupoRisco']['ativo'] ? array(1 => 'Ativo') : array(0 => 'Inativo') ) ) ); ?>
	<?php echo $this->BForm->input('GrupoRiscoExterno.codigo_externo', array('label' => 'Código Externo (*)', 'class' => 'input-large' )); ?>
  	<?php echo $this->BForm->hidden('codigo'); ?>
  	<?php echo $this->BForm->hidden('codigo_grupos_riscos',array('value'=>$this->data['GrupoRisco']['codigo'])); ?>
  	<?php echo $this->BForm->hidden('codigo_cliente',array('value'=>$this->params['pass'][0])); ?>
  </div>  
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'grupos_riscos', 'action' => 'index_externo'), array('class' => 'btn')); ?>
</div>