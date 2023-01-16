<?php echo $bajax->form('Risco', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Risco', 'element_name' => 'buscar_fontes_geradoras_riscos', 'codigo_fonte_geradora' => $codigo_fonte_geradora), 'divupdate' => '.form-procurar')) ?>
    <div class='well'>   
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'label' => 'Código', 'type' => 'text'));?>  
            <?php echo $this->BForm->input('nome_agente', array('class' => 'input-xlarge', 'label' => 'Risco'));?>  
            <?php echo $this->BForm->input('codigo_grupo', array('class' => 'input-small', 'label' => 'Grupo', 'options' => $grupo_risco, 'default' => ' ', 'empty' => 'Todos'));?>  
        </div>   
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar', 'javascript:void(0)', array('class' => 'btn', 'id' => 'limpar-filtro-busca-risco')); ?>
    </div>
<?php echo $this->BForm->end() ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
        atualizaLista("buscar_fontes_geradoras_riscos");

        jQuery("#limpar-filtro-busca-risco").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Risco/element_name:buscar_fontes_geradoras_riscos/codigo_fonte_geradora:'.$codigo_fonte_geradora.'/" + Math.random())
        });
    });


    function atualizaLista(destino) {
        var div = jQuery("div#buscar_fontes_geradoras_riscos-lista");
        bloquearDiv(div);
        div.load(baseUrl + "fontes_geradoras/listagem_fontes_geradoras_riscos/" + destino.toLowerCase() + "/'.$codigo_fonte_geradora.'/" + Math.random());
    }',false);
?>

