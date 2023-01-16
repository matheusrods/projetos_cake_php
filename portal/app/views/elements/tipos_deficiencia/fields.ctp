 <div class='row-fluid inline'>
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge')); ?>
	
	<?php if(empty($this->passedArgs)): ?>
		<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
	<?php else: ?>
		<?php echo $this->BForm->input('ativo', array('label' => 'Status (*)', 'class' => 'input', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
	<?php endif;  ?>
	</div>  
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('classificacao', array('label' => 'Classificação(*)', 'class' => 'input-xxlarge', 'default' => '', 'empty' => 'Selecione','options' => $classificacao)); ?>
</div>
	
	<?php echo $this->BForm->hidden('codigo', array('value' => !empty($this->data['TipoDeficiencia']['codigo'])? $this->data['TipoDeficiencia']['codigo'] : '')); ?>
  
  
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'tipos_deficiencia', 'action' => 'index'), array('class' => 'btn')); ?>
</div>