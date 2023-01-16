<div id="buscar_epc"></div>
<div id="busca-lista-epc"></div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaEpc();
    });

    function atualizaListaEpc() {
        var div = jQuery("div#busca-lista-epc");
        bloquearDiv(div);
        div.load(baseUrl + "epc/listagem_buscar_epc/'.$linha.'/'.$codigo_risco.'/" + Math.random());
    }

    ',false);
?>


