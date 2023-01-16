 <div class='row-fluid inline'>
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge')); ?>
	
	<?php if(empty($this->passedArgs)): ?>
		<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
	<?php else: ?>
		<?php echo $this->BForm->input('ativo', array('label' => 'Status (*)', 'class' => 'input', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
	<?php endif;  ?>
</div>  

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('codigo_tipo_afastamento', array('label' => 'Tipo de Afastamento (*)', 'class' => 'input-xxlarge', 'default' => '', 'empty' => 'Selecione','options' => $tipos_afastamento)); ?>
</div>  
 
<div class='row-fluid inline'>
	<h5>(e-Social)</h5>
	<?php echo $this->BForm->input('codigo_esocial', array('label' => 'Item Tabela 18 (*)', 'class' => 'input-xxlarge', 'empty' => 'Selecione', 'options' => $esocial));?>
</div>  
	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['MotivoAfastamento']['codigo'])? $this->data['MotivoAfastamento']['codigo'] : '')); ?>
  
  
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'motivos_afastamento', 'action' => 'index'), array('class' => 'btn')); ?>
</div>