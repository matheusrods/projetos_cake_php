<div class='well'>
    <?php echo $bajax->form('Referencia', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Referencia', 'element_name' => 'alvos_janelas'), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('referencias/fields_filtros') ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php 
    echo $this->Javascript->codeBlock('
    $(document).ready(function(){
        atualizaListaReferencias("alvos_janelas");
        $("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "/filtros/limpar/model:Referencia/element_name:alvos_janelas/" + Math.random())
        });

        $("a#filtros").click(function(){
            $("div#filtros").slideToggle("fast");
        });
        function atualizaListaReferencias(destino) {
            var div = jQuery("div.lista");
            bloquearDiv(div);           
            div.load(baseUrl + "referencias/listagem/0/1/destino/" + Math.random());            
        }
    });', false);
?>