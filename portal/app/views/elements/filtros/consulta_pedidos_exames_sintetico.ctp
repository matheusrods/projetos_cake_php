<div class='well'>
	<h5><?= $this->Html->link((!empty($this->data['PedidoExame']['codigo_cliente']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
	<div id='filtros'>
		
		<?php echo $bajax->form('PedidoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PedidoExame', 'element_name' => 'baixa_exames_sintetico'), 'divupdate' => '.form-procurar')) ?>
		
		<?= $this->element('consulta_pedidos_exames/fields_filtros') ?>

		<div class="row-fluid inline">
	        <span class="label label-info">Agrupar por:</span>
	        <div id='agrupamento'>
	            <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $tipos_agrupamento, 'default' => 5, 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
	        </div>
	    </div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-consulta-pedido-exame', 'class' => 'btn')) ;?>
		<?php echo $this->BForm->end() ?>
	</div>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "consulta_pedidos_exames/baixa_exames_sintetico_listagem/" + Math.random());

		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

		jQuery("#limpar-filtro-consulta-pedido-exame").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PedidoExame/element_name:baixa_exames_sintetico/" + Math.random());
            $("#PedidoExameCodigoCliente").val("");
        });
    });', false);
?>
<?php if (!empty($this->data['PedidoExame']['codigo_cliente'])): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>