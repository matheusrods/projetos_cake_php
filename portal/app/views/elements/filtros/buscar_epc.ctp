
<?php echo $bajax->form('Epc', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Epc', 'element_name' => 'buscar_epc', 'codigo_risco' => $codigo_risco), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'label' => 'EPC'));?>  
    </div>   
    <div class='form-actions'>
         <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-primary')); ?>
         <?php echo $html->link('Limpar', 'javascript:void(0)', array('class' => 'btn', 'id' => 'limpar-filtro-busca-epc')); ?>
             </div>
<?php echo $this->BForm->end() ?>

<div id="busca-lista-epc"></div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        
        atualizaListaEpc("listagem_buscar_epc");

        jQuery("#limpar-filtro-busca-epc").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Epc/element_name:buscar_epc/codigo_risco:'.$codigo_risco.'/" + Math.random())
        });
    });


    function atualizaListaEpc(destino) {
        
        var div = jQuery("div#busca-lista-epc");
        bloquearDiv(div);
        div.load(baseUrl + "grupos_exposicao_riscos/listagem_buscar_epc/" + destino.toLowerCase() + "/'.$codigo_risco.'/" + Math.random());
    }

    ',false);
?>