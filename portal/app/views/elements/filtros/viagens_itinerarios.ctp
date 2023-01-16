<div class='well'>
	<?php echo $this->Bajax->form('Itinerarios', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Itinerarios', 'element_name' => 'viagens_itinerarios'), 'divupdate' => '.form-procurar')) ?>
  		<div class='row-fluid inline'>
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'Itinerarios') ?>
			<?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small', 'label' => false, 'placeholder' => 'SM', 'type' => 'text')) ?>
			<?php echo $this->BForm->input('veic_placa', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa', 'type' => 'text')) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
			<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
		</div>
	<?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
		atualizaListaItinerarios();

		$("#limpar-filtro").click(function(){
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:Itinerarios/element_name:viagens_itinerarios/" + Math.random())
		});
	});', false);
?>