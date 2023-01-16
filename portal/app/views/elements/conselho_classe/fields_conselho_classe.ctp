<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('ConselhoProfissional.codigo'); ?>
	<?php echo $this->BForm->input('ConselhoProfissional.descricao', array('label' => 'Descrição (*)', 'class' => 'input-xxlarge')); ?>
</div>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'medicos', 'action' => 'conselho_classe'), array('class' => 'btn')); ?>
</div>
