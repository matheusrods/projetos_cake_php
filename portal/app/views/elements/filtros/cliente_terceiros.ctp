<div class='well'>
  <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => "cliente_terceiros"), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->element('clientes/fields_filtros') ?>
  <?php echo $this->BForm->end() ?>
</div> 

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    
        atualizaListaClientesTerceiros();
        setup_datepicker();
        jQuery("#limpar-filtro-clientes").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:cliente_terceiros/" + Math.random())
        });
        
        jQuery(".codigo-cliente-tipo").bind("change",
            function() {
                jQuery.ajax({
                    "url": baseUrl + "/clientes_sub_tipos/combo/" + jQuery(this).val() + "/" + Math.random(),
                    "success": function(data) {
                        jQuery(".codigo-cliente-sub-tipo").html(data).val();
                    }
                });
            }
        );

        function atualizaListaClientesTerceiros() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "clientes/listagem_terceiros/" + Math.random());
        }


    });', false);
?>