<?php  echo $this->Bajax->form('Veiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Veiculo', 'element_name' => 'posicoes'), 'divupdate' => '#filtros')) ?>
<div class='form-procurar'>	
	<div class='well'>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'Veiculo'); ?>
			<?php echo $this->BForm->input('Veiculo.placa', array('label' => false, 'class' => 'placa-veiculo input-small','placeholder' => 'Placa' , 'name' => "data[Veiculo][placa]")); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id' => 'filtrar')) ?>
		<?php echo $this->BForm->end();?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock("
	 $(document).ready(function() {    
	 	carregarViagemParaIncluirPosicao();
		setup_mascaras();
});", false); ?> 
