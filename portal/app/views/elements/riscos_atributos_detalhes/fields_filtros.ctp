<div class="row-fluid inline">
	<?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descriação', 'label' => false)) ?>
	<?php echo $this->BForm->input('ativo', array('class' => 'input-xsmall', 'label' => false, 'options' => array('' => 'Ambos Status', '1' => 'Ativos', '0' => 'Inativos'), 'empty' => 'Ambos Status')); ?>  
</div>        