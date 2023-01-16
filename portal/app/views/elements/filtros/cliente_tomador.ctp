<div class='well'>
  <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => "cliente_tomador"), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->element('clientes/tomador_servico') ?>
  <?php echo $this->BForm->end() ?>
</div> 

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    
        atualizaListaClienteTomador();
        setup_datepicker();
        jQuery("#limpar-filtro-clientes").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:cliente_tomador/" + Math.random())
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

        function atualizaListaClienteTomador() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "clientes/listagem_tomador/" + Math.random());
        }


    });', false);
?>