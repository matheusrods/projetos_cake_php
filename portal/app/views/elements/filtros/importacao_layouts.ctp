<div class='well'>
	<div id='filtros'>
		<?php echo $bajax->form('IntUploadCliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'IntUploadCliente', 'element_name' => 'importacao_layouts'), 'divupdate' => '.form-procurar')) ?>
		<?php echo $this->element('importacao_layouts/fields_filtros') ?>
		<?php echo $this->BForm->end() ?>
	</div>
</div>
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<script>
	function atualizaListaLayouts() {
			const div = jQuery("div.lista");
			bloquearDiv(div);
			div.load(baseUrl + "importacao_layouts/listagem");
	}
	jQuery(document).ready(function() {
		atualizaListaLayouts();
		jQuery("#limpar-filtro").click(function() {
			bloquearDiv(jQuery(".form-procurar"));
			jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:IntUploadCliente/element_name:importacao_layouts/" + Math.random())
		});

	
	});
</script>