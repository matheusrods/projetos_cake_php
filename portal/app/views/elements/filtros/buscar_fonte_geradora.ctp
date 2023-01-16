
<?php echo $bajax->form('FonteGeradora', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FonteGeradora', 'element_name' => 'buscar_fonte_geradora', 'linha' => $linha, 'codigo_risco' => $codigo_risco), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('nome', array('class' => 'input-xlarge', 'label' => 'Fonte Geradora'));?>  
    </div>   
    <div class='form-actions'>
         <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-primary')); ?>
         <?php echo $html->link('Limpar', 'javascript:void(0)', array('class' => 'btn', 'id' => 'limpar-filtro-busca-fonte_geradora')); ?>
             </div>
<?php echo $this->BForm->end() ?>

<div id="busca-lista-fonte-geradora"></div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        
        atualizaListaFonteGeradora("listagem_buscar_fonte_geradora");

        jQuery("#limpar-filtro-busca-fonte_geradora").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FonteGeradora/element_name:buscar_fonte_geradora/codigo_risco:'.$codigo_risco.'/" + Math.random())
        });
    });

    function atualizaListaFonteGeradora() {
        var div = jQuery("div#busca-lista-fonte-geradora");
        bloquearDiv(div);
        div.load(baseUrl + "grupos_exposicao_riscos/listagem_buscar_fonte_geradora/'.$linha.'/'.$codigo_risco.'/" + Math.random());
    }

    ',false);
?>

