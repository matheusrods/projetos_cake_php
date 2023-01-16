<div class='well'>
	<?php echo $bajax->form('Funcionarios', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Funcionarios', 'element_name' => 'funcionarios_liberacao_trabalho'), 'divupdate' => '.form-procurar')) ?>
		
		<?php echo $this->Buonny->input_grupo_economico($this, 'Funcionarios', $unidades, $setores, $cargos); ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('cpf', array('label' => 'CPF', 'placeholder' => false, 'class' => 'input-medium cpf')); ?>
			<?php echo $this->BForm->input('matricula', array('label' => 'Matricula', 'placeholder' => false, 'class' => 'input-medium')); ?>
			<?php echo $this->BForm->input('grupo_trabalho', array('label' => 'Grupo Trabalho', 'placeholder' => 'Grupo Trabalho', 'class' => 'input-small', 'options' => $grupo_trabalho)); ?>
		</div>

		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-clientes-funcionarios', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>	
<?php 
	
	echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "funcionarios/listagem_funcionario_liberacao/" + Math.random());
		jQuery("#limpar-filtro-clientes-funcionarios").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Funcionarios/element_name:funcionarios_liberacao_trabalho/" + Math.random())
        });
    });', false);
?>