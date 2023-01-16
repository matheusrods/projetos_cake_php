<div class='well'>
	<div id='filtros'>
		<?php echo $bajax->form('MapLayout', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MapLayout', 'element_name' => 'criacao_layouts'), 'divupdate' => '.form-procurar')) ?>
		<?php echo $this->element('criacao_layouts/fields_filtros') ?>
		<?php echo $this->BForm->end() ?>
	</div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<script>
	jQuery(document).ready(function() {
		atualizaListaLayouts();
		jQuery("#limpar-filtro").click(function() {
			bloquearDiv(jQuery(".form-procurar"));
			jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MapLayout/element_name:criacao_layouts/" + Math.random())
		});

		function atualizaListaLayouts() {
			const div = jQuery("div.lista");
			bloquearDiv(div);
			div.load(baseUrl + "criacao_layouts/listagem");
		}
	});
</script>