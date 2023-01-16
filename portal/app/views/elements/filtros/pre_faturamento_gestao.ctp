<div class='well'>
	<?php echo $bajax->form('PreFaturamento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PreFaturamento', 'element_name' => 'pre_faturamento_gestao'), 'divupdate' => '.form-procurar')) ?>
		
	<?php echo $this->element('pre_faturamento/gestao_fields_filtros'); ?>

	<?php echo $this->BForm->button('Buscar', array('div' => false, 'class' => 'btn', 'id' => 'buscar')) ?>	
	
	<?php echo $html->link('Limpar filtros', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-danger', 'style'=>'color: #fff')) ;?>

	<?php echo $this->BForm->end(); ?>
</div>	

<script>
    $(document).ready(function(){

		function atualizaLista() { 
			var div = $("div.lista");
			bloquearDiv(div);
			div.load(baseUrl + "pre_faturamento/gestao_listagem/" + Math.random());			
		}

		$("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "filtros/limpar/model:PreFaturamento/element_name:pre_faturamento_gestao/" + Math.random())
		});
		
		$("#buscar").click(function(){	
			/*if($("#PreFaturamentoCodigoCliente").val()== null || $("#PreFaturamentoCodigoCliente").val() ==""){      		
        		$("input[name='data[PreFaturamento][codigo_cliente]']").css({borderColor: "red"});    
				return false; 
			}else{ 	*/
				if($("#PreFaturamentoStatus").val() == 0){      		
					$("#PreFaturamentoStatus").css({borderColor: "red"});    
					return false; 
				}else{
					$("form").submit(function(){				
						atualizaLista();				
					});
				}
			//}
		});
		
		if( $("#PreFaturamentoCodigoCliente").val() != "" ){
			atualizaLista();
		}

    });
</script>


 