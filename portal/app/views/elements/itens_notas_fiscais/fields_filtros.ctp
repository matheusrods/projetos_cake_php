<div class="row-fluid inline">
	<?php if (isset($grupo_empresas) && $grupo_empresas): ?>
		<?php echo $this->Buonny->input_grupo_empresas($this, $grupos_empresas, $empresas); ?>
	<?php endif ?>
	<?php if (isset($mes_ano) && $mes_ano): ?>
		<?php echo $this->BForm->input('mes', array('type' => 'select', 'options' => $meses, 'class' => 'input-small', 'label' => false, 'default' => date('m'))); ?>
	<?php endif ?>
	<?php echo $this->BForm->input('ano', array('type' => 'select', 'options' => $anos, 'class' => 'input-small', 'label' => false, 'default' => date('Y'))); ?>
	<?php echo $this->Buonny->input_codigo_cliente($this) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_gestor', array('type' => 'select', 'options' => $gestores, 'class' => 'input-large', 'label' => false, 'empty' => 'Todos Gestores')); ?>
	<?php if(empty($authUsuario['Usuario']['codigo_corretora'])): ?>
		<?php echo $this->BForm->input('codigo_corretora', array('type' => 'select', 'options' => $corretoras, 'class' => 'input-large', 'label' => false, 'empty' => 'Todas Corretoras')); ?>
	<?php endif; ?>
	<?php if(empty($authUsuario['Usuario']['codigo_seguradora'])): ?>
		<?php echo $this->BForm->input('codigo_seguradora', array('type' => 'select', 'options' => $seguradoras, 'class' => 'input-large', 'label' => false, 'empty' => 'Todas Seguradoras')); ?>
	<?php endif; ?>
	<?php if(empty($authUsuario['Usuario']['codigo_filial'])): ?>
		<?php echo $this->BForm->input('codigo_filial', array('type' => 'select', 'options' => $filiais, 'class' => 'input-large', 'label' => false, 'empty' => 'Todas Filiais')); ?>
	<?php endif; ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_grupo_economico', array('type' => 'select', 'options' => $grupos_economicos, 'class' => 'input-large', 'label' => false, 'empty' => 'Todos Grupos Econ.')); ?>
	<?php echo $this->BForm->input('codigo_produto', array('type' => 'select', 'options' => $produtos, 'class' => 'input-large', 'label' => false, 'empty' => 'Todos Produtos')); ?>
</div>