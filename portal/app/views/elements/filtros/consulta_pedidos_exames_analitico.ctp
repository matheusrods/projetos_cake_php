<div class='well'>
	<?php echo $bajax->form('PedidoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PedidoExame', 'element_name' => 'baixa_exames_analitico'), 'divupdate' => '.form-procurar')) ?>
	<?= $this->element('consulta_pedidos_exames/fields_filtros') ?>

	<div style="position: relative;float: right;margin-right: 493px;margin-top: -50px;">

	<?php echo $this->BForm->input('tipo_exame', array('options' => $tipos_exames, 'empty' => 'Selecione Tipo Exame', 'class' => 'input-medium', 'label' => false)); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-consulta-pedido-exame', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "consulta_pedidos_exames/baixa_exames_analitico_listagem/" + Math.random());

		jQuery("#limpar-filtro-consulta-pedido-exame").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PedidoExame/element_name:baixa_exames_analitico/" + Math.random());
            $("#PedidoExameCodigoCliente").val("");
        });
    });', false);
?>