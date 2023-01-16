<div id="buscar_efeitos_criticos"></div>
<div id="busca-lista-efeitos_criticos"></div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaEfeitosCriticos();
    });

    function atualizaListaEfeitosCriticos() {
        var div = jQuery("div#busca-lista-efeitos_criticos");
        bloquearDiv(div);
        div.load(baseUrl + "riscos_atributos_detalhes/listagem_buscar_efeitos_criticos/'.$linha.'/'.$codigo_risco.'/" + Math.random());
    }

    ',false);
?>


