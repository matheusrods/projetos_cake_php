<div class='well'>
	<?php echo $bajax->form('ClienteFuncionario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteFuncionario', 'element_name' => 'funcionarios_cliente'), 'divupdate' => '.form-procurar')) ?>
	<?= $this->element('clientes_funcionarios/fields_filtros_funcionarios') ?>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-clientes-funcionarios', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>	
<?php 
	$btn_importacao_pedidos_exame = "<a href='/portal/importar/importar_pedido_exame' id='link_importacao_pedidos_exame' class='btn btn-warning' title='Importar Pedidos Exame'><i class='icon-plus icon-white'></i>Importar Pedidos Exame</a>";
	echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	var importacao_pedidos_exame_botao = $("#importacao_pedidos_exame");
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "clientes_funcionarios/listagem/" + Math.random());
		jQuery("#limpar-filtro-clientes-funcionarios").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteFuncionario/element_name:funcionarios_cliente/" + Math.random())
            if(importacao_pedidos_exame_botao.html()) {
            	importacao_pedidos_exame_botao.html(" ");
            }
        });
        $(function() {
			var codigo_cliente = $("#ClienteFuncionarioCodigoCliente");
			var message_error = $(".help-block.error-message");
						
			if(codigo_cliente.val() > 0 && !message_error.is(":visible")) {
				criaBotaoImpPedidosExame(importacao_pedidos_exame_botao);
			}

			codigo_cliente.blur(function(){
				if(codigo_cliente.val() > 0) {
					criaBotaoImpPedidosExame(importacao_pedidos_exame_botao);
				} else {
					importacao_pedidos_exame_botao.html(" ");
				}
			});

			function criaBotaoImpPedidosExame(importacao_pedidos_exame_botao) {
				var btn_importacao_pedidos_exame = "' . $btn_importacao_pedidos_exame . '";
				importacao_pedidos_exame_botao.html(btn_importacao_pedidos_exame);
				$("#link_importacao_pedidos_exame").attr("href", "/portal/importar/importar_pedido_exame/" + codigo_cliente.val())
			}
		});
    });', false);
?>