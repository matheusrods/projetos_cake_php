<?php echo $this->Buonny->input_grupo_economico($this, 'ClienteFuncionario', $unidades, $setores, $cargos); ?>

<div class="row-fluid inline">
	<?php echo $this->BForm->input('ativo', array(	'label' => 'Status', 
													'placeholder' => 'Status', 
													'class' => 'input-small', 
													'options' => $status_matricula)
									); 
	?>
</div>