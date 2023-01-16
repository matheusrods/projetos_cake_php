<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('codigo_cliente_alocacao', array('value' => $this->data['Unidade']['codigo'])); ?>
	<?php echo $this->BForm->input('codigo', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Código', 'type' => 'text')); ?>
	<?php echo $this->BForm->input('codigo_setor', array('class' => 'input-xlarge bselect2', 'label' => false, 'options' => $setor, 'empty' => 'Setores')); ?>
	<?php echo $this->BForm->input('codigo_cargo', array('class' => 'input-xlarge bselect2', 'label' => false, 'options' => $cargo, 'empty' => 'Cargos')); ?>
	<?php echo $this->BForm->input('codigo_grupo_homogeneo', array('class' => 'input-xlarge bselect2', 'label' => false, 'options' => $grupo_homogeneo, 'empty' => 'Grupo Homogêneo')); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->Buonny->input_nome_funcionario_sem_label($this, 'GrupoExposicao', null, $this->data['Unidade']['codigo']);?>
</div>