<div class='well'>   
    <?php $input_id = !empty($input_id)? $input_id : $this->data['Risco']['input_id'];?>
    <?php $input_display = !empty($input_display)? $input_display : $this->data['Risco']['input_display'];?>
    
    <?php echo $bajax->form('Risco', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Risco', 'element_name' => 'buscar_risco', 'input_id' => $input_id, 'input_display' => $input_display), 'divupdate' => '.form-procurar')) ?>

        <?php echo $this->element('riscos/fields_filtros') ?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Risco/element_name:buscar_risco/input_id:'.$input_id.'/input_display:'.$input_display.'/" + Math.random())
        });
        atualizaLista("buscar_risco", "'.$input_id.'","'.$input_display.'");
    });


    function atualizaLista(destino, input_id, input_display) {
        var div = jQuery("div#lista");
        bloquearDiv(div);
        div.load(baseUrl + "riscos/buscar_listagem/" + destino.toLowerCase() + "/input_id:" + input_id + "/input_display:" + input_display + "/" + Math.random());
    }', false);
?>