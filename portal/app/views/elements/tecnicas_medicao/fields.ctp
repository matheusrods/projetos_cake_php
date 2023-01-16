<div class="well">
    <div class='row-fluid inline'>
		<?php echo $this->BForm->input('nome', array('label' => 'Nome (*)', ', true, class' => 'input-xxlarge')); ?>
		<?php echo $this->BForm->input('abreviacao', array('label' => 'Abreviação (*)', ', true, class' => 'input-xxlarge')); ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('descricao', array('type'=>'textarea','label' => false, 'div' => false, 'label' => 'Descrição', 'class' => 'input-xxlarge')); ?>
	</div>
</div>