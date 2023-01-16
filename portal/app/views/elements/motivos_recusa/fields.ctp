<div class='row-fluid inline'>
	<?php echo $this->BForm->input('MotivoRecusa.descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge')); ?>
	<?php if(empty($this->passedArgs[0])): ?>
		<?php echo $this->BForm->hidden('MotivoRecusa.ativo', array('value' => 1)); ?>
	<?php else: ?>
		<?php echo $this->BForm->input('MotivoRecusa.ativo', array('label' => 'Status (*)', 'class' => 'input', 'default' => '', 'empty' => 'Status', 'options' => array(1 => 'Ativo', 0 => 'Inativo'))); ?>
	<?php endif;  ?>
	<?php echo $this->BForm->hidden('MotivoRecusa.codigo', array('value' =>  !empty($this->data['MotivoRecusa']['codigo'])? $this->data['MotivoRecusa']['codigo'] : '')); ?>
  </div>  

 <div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('controller' => 'motivos_recusa', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
