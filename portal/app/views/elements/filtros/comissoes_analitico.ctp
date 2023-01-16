<?php echo $this->element("transacoes_de_recebimento/filtros_comissoes_analitico"); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery(".comissoes_analitico_listagem");
        bloquearDiv(div);
        div.load(baseUrl + "/transacoes_de_recebimento/comissoes_analitico_listagem/" + Math.random());
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Tranrec/element_name:comissoes_analitico/" + Math.random())
        });
    });', false);
?>