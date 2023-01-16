<div class='well'>
	<?php echo $bajax->form('AtestadoFuncionario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AtestadoFuncionario', 'element_name' => 'atestados_funcionarios'), 'divupdate' => '.form-procurar')) ?>
	<?= $this->element('atestados/fields_filtros_funcionarios') ?>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-atestados', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end() ?>
</div>	
<?php
	$btn_importacao_atestados = "<a href='/portal/importar/importar_atestado/' id='link_importacao_atestados' class='btn btn-warning' title='Importar Absenteísmo'><i class='icon-plus icon-white'></i>Importar Absenteísmo</a>";
	echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	var importacao_atestados_botao = $("#importacao_atestados");
		setup_datepicker(); 
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "atestados/lista_funcionarios/" + Math.random());
		jQuery("#limpar-filtro-atestados").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AtestadoFuncionario/element_name:atestados_funcionarios/" + Math.random())

            if(importacao_atestados_botao.html()) {
            	importacao_atestados_botao.html(" ");
            }
		});
		$(function() {
			var codigo_cliente = $("#AtestadoFuncionarioCodigoCliente");
						
			if(codigo_cliente.val() > 0) {
				criaBotaoImpAtestados(importacao_atestados_botao);
			}

			codigo_cliente.blur(function(){
				if(codigo_cliente.val() > 0) {
					criaBotaoImpAtestados(importacao_atestados_botao);
				} else {
					importacao_atestados_botao.html(" ");
				}
			});

			function criaBotaoImpAtestados(importacao_atestados_botao) {
				var btn_importacao_atestados = "' . $btn_importacao_atestados . '";
				importacao_atestados_botao.html(btn_importacao_atestados);
				$("#link_importacao_atestados").attr("href", "/portal/importar/importar_atestado/" + codigo_cliente.val())
			}
		});
    });', false);
?>