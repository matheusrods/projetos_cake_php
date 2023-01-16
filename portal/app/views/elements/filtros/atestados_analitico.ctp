<div class='well'>
	<?php echo $bajax->form('Atestado', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Atestado', 'element_name' => 'atestados_analitico'), 'divupdate' => '.form-procurar')) ?>
	<?= $this->element('atestados/fields_filtros') ?>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-atestados', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "atestados/analitico_listagem/" + Math.random());

		jQuery("#limpar-filtro-atestados").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Atestado/element_name:atestados_analitico/" + Math.random())
        });
    });', false);
?>