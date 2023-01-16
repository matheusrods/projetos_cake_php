 <div class='row-fluid inline'>
	<?php echo $this->BForm->input('descricao', array('label' => 'Nome Comercial (*)', 'class' => 'input-xxlarge')); ?>
	
	<?php if(empty($this->passedArgs)): ?>
		<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
	<?php else: ?>
		<?php echo $this->BForm->input('ativo', array('label' => 'Status (*)', 'class' => 'input', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
	<?php endif;  ?>
</div>  

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('principio_ativo', array('label' => 'Princípio Ativo (*)', 'class' => 'input-xxlarge'));?>
	<?php echo $this->BForm->input('codigo_laboratorio', array('label' => 'Fabricante (*)', 'class' => 'input-xxlarge', 'empty' => 'Selecione','options' => $laboratorios)); ?>
</div>  

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('codigo_apresentacao', array('label' => 'Apresentação (*)', 'class' => 'input-xxlarge', 'empty' => 'Selecione','options' => $apresentacoes)); ?>
</div> 

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('posologia', array('label' => 'Posologia', 'class' => 'input-xxlarge'));?>
</div>

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('codigo_barras', array('label' => 'Código de Barras', 'class' => 'input-xlarge'));?>
</div>  

	<?php echo $this->BForm->hidden('codigo', array('value' =>  !empty($this->data['Medicamento']['codigo'])? $this->data['Medicamento']['codigo'] : '')); ?>
  
  
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'medicamentos', 'action' => 'index'), array('class' => 'btn')); ?>
</div>