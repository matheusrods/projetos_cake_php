<div class='well'>

	<div id='filtros'>
		<?php echo $bajax->form('TCveiChecklistVeiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TCveiChecklistVeiculo', 'element_name' => 'checklist_veiculo'), 'divupdate' => '.form-procurar')) ?>
		<div class='row-fluid inline'>
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'TCveiChecklistVeiculo') ?>
			<?php echo $this->BForm->input('veiculo_placa', array('class' => 'input-small placa-veiculo', 'label' => 'Placa Veículo', 'placeholder' => 'Placa')) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-primary')); ?>
			<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		</div>
		<?php echo $this->BForm->end() ?>
	</div>
	
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();

		atualizarChecklistVeiculo(".dados");
		$("#limpar-filtro").click(function(){	
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:TCveiChecklistVeiculo/element_name:checklist_veiculo/" + Math.random());
		});
	
	});', false);
?>
