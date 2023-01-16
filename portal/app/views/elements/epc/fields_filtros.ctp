<div class="row-fluid inline">
	<?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'placeholder' => 'Nome', 'label' => false)) ?>
	<?php echo $this->BForm->input('ativo', array('class' => 'input-xsmall', 'label' => false, 'options' => array('' => 'Ambos Status', '1' => 'Ativos', '0' => 'Inativos'), 'empty' => 'Ambos Status')); ?>  
</div>        