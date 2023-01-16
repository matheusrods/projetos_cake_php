<div class='well'>
	<?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'pre_faturamento'), 'divupdate' => '.form-procurar')) ?>
		
	<?php echo $this->element('clientes/pre_faturamento_fields_filtros'); ?>

	<div id="msgRelatorio" style="display: none;">
		<p style="color:red;font-style: italic;font-size: 11px;">É necessario buscar para poder gerar a exibição escolhida.</p>
	</div>

	<?php echo $this->BForm->button('Buscar', array('div' => false, 'class' => 'btn', 'id' => 'buscarRelatorio')) ?>
	
	<?php 
	$extensao = (!isset($this->data['Cliente']['exibicao'])) ? 1 : $this->data['Cliente']['exibicao'];
	if(!empty($extensao)){
		if($extensao == 1){
			$extensao = "csv";
		}else if($extensao == 2){
			$extensao = "pdf";
		}
	}
	
	echo $html->link('Gerar relatório', array('controller' => 'Clientes', 'action' => 'listagem_pre_faturamento/', $extensao), array('class'=>'btn btn-primary', 'style'=>'color: #fff', 'id' => 'gerarRelatorio'))	?>
	<?php //esse botao fica em hide para depois ficar bloqueado ?>
	<?php echo $html->link('Gerar relatório', 'javascript:void(0)', array('id' => 'bloqueaRelatorio', 'class' => 'btn btn-primary', 'style'=>'color: #fff', 'readonly' => true)) ;?>
	<?php echo $html->link('Limpar filtros', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-danger', 'style'=>'color: #fff')) ;?>
	<?php echo $this->BForm->end(); ?>
</div>	

<script>
    $(document).ready(function(){	
    	var botaoOk = $('#gerarRelatorio');
    	$('#bloqueaRelatorio').hide();
		//desabilita botão caso a busca não estiver listada
		if($("div.lista").html().length == 0){
			$('#gerarRelatorio').hide();
	      	$('#bloqueaRelatorio').show();
	      	$("#bloqueaRelatorio").attr('disabled', true);
		}
		//desabilita botão caso o tipo seja modificado
		$("input[type=radio]").change(function(){
			$('#gerarRelatorio').hide();
	      	$('#bloqueaRelatorio').show();
	      	$("#bloqueaRelatorio").attr('disabled', true);
	      	$('#msgRelatorio').show();
		});

		$('#ClienteCodigoUnidade').change(function() {
			$('#gerarRelatorio').hide();
	      	$('#bloqueaRelatorio').show();
	      	$("#bloqueaRelatorio").attr('disabled', true);
			$('#msgRelatorio').show();
		});

		function atualizaLista() { 
			var div = $("div.lista");
			bloquearDiv(div);
			div.load(baseUrl + "clientes/listagem_pre_faturamento/" + Math.random());			
		}

		$("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "filtros/limpar/model:Cliente/element_name:pre_faturamento/" + Math.random())
		});
		
		$("#buscarRelatorio").click(function(){	
			// $("#gerarRelatorio").attr('disabled', false);		
			$('#gerarRelatorio').show();
			$('#bloqueaRelatorio').hide();
			$('#msgRelatorio').hide();
			if($("#ClienteCodigoCliente").val()== null || $("#ClienteCodigoCliente").val() ==""){        		
        		$("input[name='data[Cliente][codigo_cliente]']").css({borderColor: "red"});    
        		return false;        		
    		} else {
    			$("form").submit(function(){				
					atualizaLista();				
				});
    		}
			
		});

		$('#ClienteFormaDeCobranca').on('change', function() {
	      	if ( this.value == 'Per Capita') {
	      		$('#gerarRelatorio').hide();
	      		$('#bloqueaRelatorio').show();
				$("#bloqueaRelatorio").attr('disabled', true);
				$('#msgRelatorio').show();		
	      	} else if (this.value == 'Exames Complementares'){
	        	$('#gerarRelatorio').hide();
	        	$('#bloqueaRelatorio').show();
	        	$("#bloqueaRelatorio").attr('disabled', true);
	        	$('#msgRelatorio').show();
	      	}
    	});

    	$("#bloqueaRelatorio").click(function(){
        	$('#msgRelatorio').show();             
    	});

    	if( $('#gerarRelatorio').is(":visible") ){
    	 	$('#gerarRelatorio').on('click', function() {
    	 		if( $('#ClienteExibicao2').is(":checked") ){
		      		var verifica_unidades = $("#ClienteCodigoUnidade :selected").val();
					if (verifica_unidades == ""){
						swal("Ops!", "Selecione a Unidade.", "error");
						return false;
					} else if($("#ClienteCodigoCliente").val()== null || $("#ClienteCodigoCliente").val() ==""){
	        		 	swal("Ops!", "Favor colocar o codigo do cliente.", "error");
	        		 	$("input[name='data[Cliente][codigo_cliente]']").css({borderColor: "red"});      
	        			return false;
	    			} else if($("#ClienteAno").val()== null || $("#ClienteAno").val() ==""){
	        		 	swal("Ops!", "Preencha o ano.", "error");
	        		 	$("input[name='data[Cliente][ano]']").css({borderColor: "red"});     
	        			return false;
	    			}
	    		}
    		});
    	}

    });
</script>


 