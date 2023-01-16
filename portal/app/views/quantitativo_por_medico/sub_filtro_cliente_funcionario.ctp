<?php echo $bajax->form('Atestado', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Atestado', 'element_name' => 'quantitativo_por_medico'), 'divupdate' => '.form-procurar')) ?>
	<?php echo $this->BForm->input('GrupoEconomico.codigo', array('type' => 'hidden', 'id' => 'codigo_grupo_economico', 'value' => $codigo_grupo_economico)); ?>
	<?php echo $this->BForm->input('codigo_unidade', array('type' => 'hidden', 'id' => 'codigo_unidade', 'value' => $codigo_unidade)); ?>
	
	<?php echo $this->BForm->input('codigo_cliente', array('value' => (isset($codigo_cliente) ? $codigo_cliente : ''), 'label' => 'Unidade', 'class' => 'input-small','options' => $lista_unidades, 'id' => 'unidades', 'style' => 'width:270px', 'empty' => 'Selecione a Unidade')); ?>
	<?php echo $this->BForm->input('codigo_setor', array('value' => (isset($codigo_setor) ? $codigo_setor : ''), 'class' => 'input-small', 'label' => 'Setores', 'options' => $lista_setores,'style' => 'width:270px', 'id' => 'setores', 'empty' => 'Selecione o Setor')); ?>
	<?php echo $this->BForm->input('codigo_cargo', array('value' => (isset($codigo_cargo) ? $codigo_cargo : ''), 'class' => 'input-small', 'label' => 'Cargos', 'options' => $lista_cargos,'style' => 'width:270px', 'id' => 'cargos', 'empty' => 'Selecione o Cargo')); ?>
	<?php echo $this->BForm->input('codigo_funcionario', array('value' => (isset($codigo_funcionario) ? $codigo_funcionario : ''), 'class' => 'input-small', 'label' => 'Funcionarios', 'options' => $lista_funcionarios, 'id' => 'funcionarios','style' => 'width:270px', 'empty' => 'Selecione o Funcionário')); ?>

	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
<?php echo $this->BForm->end() ?>
	    
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function() {
		if($("#codigo_grupo_economico").val()) {
			$(".lista").load(baseUrl + "quantitativo_por_medico/listagem/" + $("#codigo_grupo_economico").val() + "/" + Math.random());
		}
    });
'); ?>