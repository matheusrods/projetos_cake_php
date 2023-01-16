<div class='well'>
	<h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
	<?php echo $bajax->form('TIpcpInformacaoPcp', array('autocomplete' => 'off', 'url' => array(
		'controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TIpcpInformacaoPcp',
		'element_name' => 'filtros_pcp'),'divupdate' => '.form-procurar')) ?>
	<?= $this->element('pcp/fields_analitico_filtros') ?>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-clientes', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
	    jQuery(document).ready(function(){
	'.(isset($isPost) && isset($codigo_cliente) && isset($filtrado) ?'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "pcp/listagem_pcp/" + Math.random());' : '').'

		var div = jQuery("div.lista");
		bloquearDiv(div);
		div.load(baseUrl + "pcp/listagem_pcp/" + Math.random());
		jQuery("#limpar-filtro-clientes").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TIpcpInformacaoPcp/element_name:filtros_pcp/validate:0/" + Math.random())
        });

		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });

	    });', false);	
?>
<?php
if (isset($isPost) && isset($codigo_cliente) && isset($filtrado)):
    echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');
endif; 
?>