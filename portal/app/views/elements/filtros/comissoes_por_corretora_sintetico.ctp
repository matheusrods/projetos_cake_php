<?php echo $this->element("transacoes_de_recebimento/filtros_comissoes_por_corretora"); ?>
<?php if($is_post) echo $this->Javascript->codeBlock('jQuery(document).ready(function(){
	var div = jQuery(".comissoes_por_corretora_sintetico_listagem");
    bloquearDiv(div);
    div.load(baseUrl + "/transacoes_de_recebimento/comissoes_por_corretora_sintetico_listagem/" + Math.random());
});');?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Tranrec/element_name:comissoes_por_corretora_sintetico/" + Math.random())
        });
    });', false);
?>