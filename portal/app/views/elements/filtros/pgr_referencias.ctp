<?php $filtrado = (isset($this->data['TPrefPgrReferencia']) ? true : false)?>
<div class='well'>

	<div id='filtros'>
		<?php echo $bajax->form('TPrefPgrReferencia', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TPrefPgrReferencia', 'element_name' => 'pgr_referencias'), 'divupdate' => '.form-procurar')) ?>
			<div class='row-fluid inline'>
				<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'TPrefPgrReferencia') ?>
				<?php echo $this->BForm->input('pref_pgpg_codigo', array('class' => 'input-small', 'label' => 'PGR', 'empty' => 'Todos', 'options' => $pgrs)) ?>
				<?php echo $this->Buonny->input_referencia($this, '#TPrefPgrReferenciaCodigoCliente', 'TPrefPgrReferencia', 'pref_refe_codigo', false, 'Alvo', 'Alvo'); ?>
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
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "pgr_referencias/listagem/" + Math.random());':'').'

		var codigo_cliente = $("#TPrefPgrReferenciaCodigoCliente").val();
		var placa = $("#TPrefPgrReferenciaPlaca").val();
			
		$("#limpar-filtro").click(function(){	
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:TPrefPgrReferencia/element_name:pgr_referencias/" + Math.random());
		});

	});', false);
?>
