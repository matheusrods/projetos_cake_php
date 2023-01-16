<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaFichasClinicas();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaClinica/element_name:selecionar_pedido_de_exame/" + Math.random())
        });
        
        function atualizaListaFichasClinicas() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "ficha_psicossocial/listagem_pedido_de_exame/" + Math.random());
        }
        
    });', false);
?>