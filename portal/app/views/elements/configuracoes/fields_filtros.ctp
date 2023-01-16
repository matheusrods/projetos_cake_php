<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_cliente_matricula', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
	<?php echo $this->BForm->input('finaliza_setor_cargo', array('class' => 'input-small', 'label' => false, 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => ' ')); ?>
</div>        