<div class='well'>
    <?php echo $bajax->form('TRotaRota', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TRotaRota', 'element_name' => 'gerar_rotas'), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('rotas/fields_filtros') ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){
            setup_mascaras();
            var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "rotas/rotas_listagem/" + Math.random());
            $("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "/filtros/limpar/model:TRotaRota/element_name:gerar_rotas/" + Math.random())
        });

        $("a#filtros").click(function(){
            $("div#filtros").slideToggle("fast");
        });
        
    });', false);
?>