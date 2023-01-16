<?php echo $this->Buonny->input_grupo_economico($this, 'AtestadoFuncionario', $unidades, $setores, $cargos); ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('ativo', array('label' => 'Status', 'placeholder' => 'Status','empty' => 'Todos', 'class' => 'input-small', 'options' => $status_matricula)); ?>
</div>