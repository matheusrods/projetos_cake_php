<div class='well'>
	<?php echo $bajax->form('UsuarioGca', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'UsuarioGca', 'element_name' => 'resultado_exame_analitico'), 'divupdate' => '.form-procurar')) ?>
	<?= $this->element('covid/fields_filtros') ?>
	
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-consulta-pedido-exame', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "covid/resultado_exame_analitico_listagem/" + Math.random());

		jQuery("#limpar-filtro-consulta-pedido-exame").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:UsuarioGca/element_name:resultado_exame_analitico/" + Math.random());
            $("#UsuarioGcaCodigoCliente").val("");
        });
    });', false);
?>