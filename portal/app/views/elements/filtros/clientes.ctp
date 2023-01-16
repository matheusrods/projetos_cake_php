<div class='well'>
  <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => "clientes"), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->element('clientes/fields_filtros') ?>
  <?php echo $this->BForm->end() ?>
</div> 

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    
        atualizaListaClientes("clientes");
        setup_datepicker();
        jQuery("#limpar-filtro-clientes").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:clientes/" + Math.random())
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
    });', false);
?>