<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo', array('class' => 'input-mini just-number', 'label' => 'Código', 'type' => 'text')) ?>
	<?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'label' => 'Fonte Geradora', 'type' => 'text')) ?>
	<?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => 'Status', 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Todos', 'default' => ' ')); ?>
</div>        