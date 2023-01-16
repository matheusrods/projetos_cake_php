<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_setor', array('class' => 'input', 'label' => false, 'options' => $setor, 'empty' => 'Setores')); ?>
	<?php echo $this->BForm->input('codigo_cargo', array('class' => 'input', 'label' => false, 'options' => $cargo, 'empty' => 'Cargos')); ?>
	<?php echo $this->BForm->input('ativo', array('class' => 'input', 'label' => false, 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => ' ')); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_risco($this, 'codigo_risco', 'Código',false,'GrupoExposicao');?>
	<?php echo $this->BForm->input('codigo_grupo', array('class' => 'input', 'label' => false, 'options' => $grupo_risco, 'empty' => 'Grupo de Risco', 'default' => '')); ?>

	<?php echo $this->BForm->hidden('unidade', array('value' => $this->data['Cliente']['codigo'])); ?>
</div>