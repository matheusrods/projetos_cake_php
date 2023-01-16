<div id="buscar_fonte_geradora"></div>
<div id="busca-lista-fonte-geradora"></div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaFonteGeradora();
    });

    function atualizaListaFonteGeradora() {
        var div = jQuery("div#busca-lista-fonte-geradora");
        bloquearDiv(div);
        div.load(baseUrl + "fontes_geradoras/listagem_buscar_fonte_geradora/'.$linha.'/'.$codigo_risco.'/" + Math.random());
    }

    ',false);
?>


