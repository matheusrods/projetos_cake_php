<div id="buscar_epi"></div>
<div id="busca-lista-epi"></div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaEpi();
    });

    function atualizaListaEpi() {
        var div = jQuery("div#busca-lista-epi");
        bloquearDiv(div);
        div.load(baseUrl + "epi/listagem_buscar_epi/'.$linha.'/'.$codigo_risco.'/" + Math.random());
    }

    ',false);
?>


