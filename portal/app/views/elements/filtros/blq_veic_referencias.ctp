<?php $filtrado = (isset($this->data['TBvreBlqVeicReferencia']) ? true : false)?>
<div class='well'>

	<div id='filtros'>
		<?php echo $bajax->form('TBvreBlqVeicReferencia', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TBvreBlqVeicReferencia', 'element_name' => 'blq_veic_referencias'), 'divupdate' => '.form-procurar')) ?>
			<div class='row-fluid inline'>
				<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'TBvreBlqVeicReferencia') ?>
				<?php echo $this->BForm->input('veic_placa', array('class' => 'input-small placa-veiculo', 'label' => 'Placa Veículo', 'placeholder' => 'Placa')) ?>
				<?php echo $this->Buonny->input_referencia($this, '#TBvreBlqVeicReferenciaCodigoCliente', 'TBvreBlqVeicReferencia', 'bvre_refe_codigo', false, 'Alvo', 'Alvo'); ?>

			</div>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
				<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
			</div>
		<?php echo $this->BForm->end() ?>
	</div>
	
</div>

<?php echo $this->Javascript->codeBlock('

	$(document).ready(function(){
		setup_mascaras();
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "blq_veic_referencias/listagem/" + Math.random());':'').'

		var codigo_cliente = $("#TBvreBlqVeicReferenciaCodigoCliente").val();
		var placa = $("#TBvreBlqVeicReferenciaPlaca").val();
			
		$("#limpar-filtro").click(function(){	
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:TBvreBlqVeicReferencia/element_name:blq_veic_referencias/" + Math.random());
		});

	});', false);
?>
