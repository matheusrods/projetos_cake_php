<div class='well'>
  	<?php echo $bajax->form('ClienteFornecedor', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteFornecedor', 'element_name' => 'cliente_fornecedor'), 'divupdate' => '.form-procurar')) ?>
    	<div class="row-fluid inline">
        	<?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Código','Prestador','ClienteFornecedor'); ?>
        	<?php echo $this->Buonny->input_codigo_cliente3($this, 'codigo_cliente', 'Cliente', null, 'ClienteFornecedor'); ?>
        	<?php echo $this->BForm->input('codigo_cliente_alocacao', array('label' => 'Unidade', 'class' => 'input-xlarge','options' => empty($unidades) ? '' : $unidades, 'empty' => 'Selecione a Unidade')); ?>    		
    		<?php echo $this->BForm->input('ativo', array('options' => array('A' => 'Ativo','I' => 'Inativo'), 'empty' => 'Todos', 'class' => 'input-medium', 'label' => 'Status Prestador')); ?>
    	</div>
  	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
  	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  	<?php echo $this->BForm->end() ?>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){
		listagem();

		function listagem(){
			var div = jQuery(".lista");
	        bloquearDiv(div);
			div.load(baseUrl + "clientes_fornecedores/listagem_cliente_por_fornecedor/" + Math.random());
		}

		jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteFornecedor/element_name:cliente_fornecedor/" + Math.random())
        });

		$(document).on("blur", "#ClienteFornecedorCodigoFornecedor", function() { 
			var codigo_fornecedor = $("#ClienteFornecedorCodigoFornecedor").val();			
			if (codigo_fornecedor) {
				preenche_name_fornecedor(codigo_fornecedor);
			}
		});

		function preenche_name_fornecedor(codigo_fornecedor){
		    var input = $("#ClienteFornecedorCodigoFornecedorCodigo");
		    $.ajax({
		        url:baseUrl + "consultas/get_fornecedores/" + codigo_fornecedor + "/" + Math.random(),
		        dataType: "json",
		        beforeSend: function() {
		            bloquearDiv(input.parent());
		        },
		        success: function(data) {
		            if (data.sucesso) {
		                var input_name_display = $("#ClienteFornecedorCodigoFornecedorCodigo").val(data.dados.razao_social);
		            } else {
		                swal("ATENÇÃO!", "Prestador Não encontrado", "warning");
		                var input_name_display = $("#ClienteFornecedorCodigoFornecedorCodigo").val("");
		            }
		        },
		        complete: function() {
		            input.parent().unblock();
		        }
		    });
		}
	});', false);
?>

</div>