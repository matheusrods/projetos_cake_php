 <div class='row-fluid inline'>
	<?php echo $this->BForm->input('descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge')); ?>
	
	<?php if(empty($this->passedArgs)): ?>
		<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
	<?php else: ?>
		<?php echo $this->BForm->input('ativo', array('label' => 'Status (*)', 'class' => 'input', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
	<?php endif;  ?>
	<?php echo $this->BForm->hidden('codigo'); ?>
  </div>  
  
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'grupos_riscos', 'action' => 'index'), array('class' => 'btn')); ?>
</div>