<div class="row-fluid inline">
	<?php echo $this->BForm->input('grupo_empresa', array('type'=>'hidden','value' => 4)) ?>
	<?php if (isset($mes_ano) && $mes_ano): ?>
		<?php echo $this->BForm->input('mes', array('type' => 'select', 'options' => $meses, 'class' => 'input-small', 'label' => false, 'default' => date('m'))); ?>
	<?php endif ?>
	<?php echo $this->BForm->input('ano', array('type' => 'select', 'options' => $anos, 'class' => 'input-small', 'label' => false, 'default' => date('Y'))); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_produto', array('type' => 'select', 'options' => $produtos, 'class' => 'input-large', 'label' => false, 'empty' => 'Todos Produtos')); ?>
</div>