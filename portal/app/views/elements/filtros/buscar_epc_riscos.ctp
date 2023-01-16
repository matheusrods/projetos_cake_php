<div class='well'>
    <?php echo $bajax->form('Risco', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Risco', 'element_name' => 'buscar_epc_riscos', 'codigo_epc' => $codigo_epc), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
                <?php echo $this->BForm->input('nome_agente', array('class' => 'input-xlarge', 'label' => 'Risco'));?>  
        <?php echo $this->BForm->input('codigo_grupo', array('class' => 'input-small', 'label' => 'Grupo', 'options' => $grupo_risco, 'default' => ' ', 'empty' => 'Todos'));?>  
    </div>   
    <div class='form-actions'>
         <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-primary')); ?>
         <?php echo $html->link('Limpar', 'javascript:void(0)', array('class' => 'btn', 'id' => 'limpar-filtro-busca-risco')); ?>
	</div>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
        atualizaLista("buscar_epc_riscos");

        jQuery("#limpar-filtro-busca-risco").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Risco/element_name:buscar_epc_riscos/codigo_epc:'.$codigo_epc.'/" + Math.random())
        });
    });


    function atualizaLista(destino) {
        var div = jQuery("div#buscar_epc_riscos-lista");
        bloquearDiv(div);
        div.load(baseUrl + "riscos/listagem_epc_riscos/" + destino.toLowerCase() + "/'.$codigo_epc.'/" + Math.random());
    }',false);
?>