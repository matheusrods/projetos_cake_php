<?php echo $this->Buonny->input_grupo_economico($this, 'ClienteFuncionario', $unidades, $setores, $cargos); ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('cpf', array('label' => 'CPF', 'placeholder' => false, 'class' => 'input-medium cpf')); ?>
	<?php echo $this->BForm->input('matricula', array('label' => 'Matricula', 'placeholder' => false, 'class' => 'input-medium')); ?>
	<?php echo $this->BForm->input('ativo', array('label' => 'Status', 'placeholder' => 'Status', 'class' => 'input-small', 'options' => $status_matricula)); ?>
</div>