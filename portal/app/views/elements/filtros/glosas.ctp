<div class='well'>
	<?php echo $bajax->form('Glosas', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Glosas', 'element_name' => 'glosas'), 'divupdate' => '.form-procurar')) ?>
	
		<?php echo $this->element('glosas/fields_filtros'); ?>

		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-glosas', 'class' => 'btn')) ;?>

	<?php echo $this->BForm->end() ?>
</div>
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	setup_datepicker();
    	atualizaLista();

    	var codigo_fornecedor = $(\'#GlosasCodigoFornecedor\').val();        
        if(codigo_fornecedor){
            preenche_name_fornecedor(codigo_fornecedor);
        }

        $(document).on(\'blur\', \'#GlosasCodigoFornecedor\', function() { 
            var codigo_fornecedor = $(\'#GlosasCodigoFornecedor\').val();
            if (codigo_fornecedor) {
                preenche_name_fornecedor(codigo_fornecedor);
            }
        });

        function preenche_name_fornecedor(codigo_fornecedor){
            var input = $("#GlosasCodigoFornecedorCodigo");        
            $.ajax({
                url:baseUrl + "consultas/get_fornecedores/" + codigo_fornecedor + "/" + Math.random(),
                dataType: "json",
                beforeSend: function() {
                    bloquearDiv(input.parent());                   
                },
                success: function(data) {                              
                    if (data.sucesso) {
                        var input_name_display = $("#GlosasCodigoFornecedorCodigo").val(data.dados.razao_social);                       
                    } else {
                        swal("ATENÇÃO!", "Prestador Não encontrado", "warning");
                        var input_name_display = $("#GlosasCodigoFornecedorCodigo").val("");
                    }
                },
                complete: function() {
                    input.parent().unblock();
                }
            });
        }
    	
    	function atualizaLista() {
			var div = jQuery(".lista");
			bloquearDiv(div);
			div.load(baseUrl + "glosas/listagem/" + Math.random());
		}

		jQuery("#limpar-filtro-glosas").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Glosas/element_name:glosas/" + Math.random())
            atualizaLista();
		});		
    });', false);
?>