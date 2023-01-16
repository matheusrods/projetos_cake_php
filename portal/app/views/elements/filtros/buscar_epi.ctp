
<?php echo $bajax->form('Epi', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Epi', 'element_name' => 'buscar_epi', 'codigo_risco' => $codigo_risco), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'label' => 'EPI'));?>  
    </div>   
    <div class='form-actions'>
         <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-primary')); ?>
         <?php echo $html->link('Limpar', 'javascript:void(0)', array('class' => 'btn', 'id' => 'limpar-filtro-busca-epi')); ?>
             </div>
<?php echo $this->BForm->end() ?>

<div id="busca-lista-epi"></div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        
        atualizaListaEpi("listagem_buscar_epi");

        jQuery("#limpar-filtro-busca-epi").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Epi/element_name:buscar_epi/codigo_risco:'.$codigo_risco.'/" + Math.random())
        });
    });


    function atualizaListaEpi(destino) {
        
        var div = jQuery("div#busca-lista-epi");
        bloquearDiv(div);
        div.load(baseUrl + "grupos_exposicao_riscos/listagem_buscar_epi/" + destino.toLowerCase() + "/'.$codigo_risco.'/" + Math.random());
    }

    ',false);
?>

