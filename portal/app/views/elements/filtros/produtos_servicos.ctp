<div class='well'>
    <?php echo $bajax->form('Consulta', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Consulta', 'element_name' => 'produtos_servicos',), 'divupdate' => '.form-procurar')) ?>
    <h5><?= $this->Html->link((!empty($this->data['Consulta']) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
  <div id='filtros'>
      <?php echo $this->element('consultas/filtros_produtos_servicos') ?>
      <?php echo $this->BForm->submit('Consultar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  </div>
    <?php echo $this->BForm->end() ?>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery(".bselect2").select2();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:Consulta/element_name:produtos_servicos/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "consultas/listagem_produtos_servicos/" + Math.random());
        }

        var codigo_fornecedor = $("#ConsultaCodigoFornecedor").val();        
	    if(codigo_fornecedor){
	        preenche_name_fornecedor(codigo_fornecedor);
	    }
    });
		
	function buscaCidade(element) {
	    var idEstado = $(element).val();
	    $.ajax({
	        type: "POST",
	        url: "/portal/enderecos/carrega_combo_cidade/" + idEstado,
	        dataType: "html",
	        beforeSend: function() { 
	            $("#cidade_combo").hide();
	            $("#carregando_cidade").show();
	        },
	        success: function(retorno) {
	            $("#ConsultaCidade").html(retorno);
	        },
	        complete: function() { 
	            $("#carregando_cidade").hide();
	            $("#cidade_combo").show();
	        }
	    });
	}

	jQuery("a#filtros").click(function(){
        jQuery("div#filtros").slideToggle("slow");
    });
		

    $(document).on("blur", "#ConsultaCodigoFornecedor", function() { 
	    var codigo_fornecedor = $("#ConsultaCodigoFornecedor").val();
	    if (codigo_fornecedor) {
	        preenche_name_fornecedor(codigo_fornecedor);
	    }
	});

	function preenche_name_fornecedor(codigo_fornecedor){
	    var input = $("#ConsultaCodigoFornecedorCodigo");
	    $.ajax({
	        url:baseUrl + "consultas/get_fornecedores/" + codigo_fornecedor + "/" + Math.random(),
	        dataType: "json",
	        beforeSend: function() {
	            bloquearDiv(input.parent());
	        },
	        success: function(data) {
	            if (data.sucesso) {
	                var input_name_display = $("#ConsultaCodigoFornecedorCodigo").val(data.dados.razao_social);
	            } else {
	                swal("ATENÇÃO!", "Prestador Não encontrado", "warning");
	                var input_name_display = $("#ConsultaCodigoFornecedorCodigo").val("");
	            }
	        },
	        complete: function() {
	            input.parent().unblock();
	        }
	    });
	}
', false);
?>

<?php if (!empty($this->data['Consulta'])): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})'); ?>
<?php endif; ?>