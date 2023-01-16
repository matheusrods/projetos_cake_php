<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('codigo_cliente', array('value' =>  $codigo_cliente)); ?>

	<?php echo $this->BForm->input('codigo_unidade', array('class' => 'input-xlarge bselect2', 'label' => false, 'options' => $unidades, 'empty' => 'Unidades')); ?>
	<?php echo $this->BForm->input('codigo_setor', array('class' => 'input-xlarge bselect2', 'label' => false, 'options' => $setor, 'empty' => 'Setores')); ?>
	<?php echo $this->BForm->input('codigo_cargo', array('class' => 'input-xlarge bselect2', 'label' => false, 'options' => $cargo, 'empty' => 'Cargos')); ?>
	 <?php echo $this->BForm->input('ativo', array('label' => false, 'class' => 'input-small', 'empty' => 'Status', 'options' => array(0 => 'Inativo', 1 => 'Ativo'))); ?>
</div>