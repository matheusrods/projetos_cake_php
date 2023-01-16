<div class='well'>   
    <?php echo $bajax->form('Medico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Medico', 'element_name' => 'buscar_medico_readonly', 'input_id' => $input_id, 'input_crm_display' => $input_crm_display, 'input_uf_display' => $input_uf_display, 'input_nome_display' => $input_nome_display, 'input_cpf_display' => $input_cpf_display), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('medicos/fields_filtros') ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Medico/element_name:buscar_medico_readonly/" + Math.random())
        });
		
        atualizaLista("buscar_medico_readonly");
    });


    function atualizaLista(destino) {
        var div = jQuery("div#busca-lista");
        bloquearDiv(div);
        div.load(baseUrl + "medicos/buscar_listagem_readonly/" + destino.toLowerCase() + "/input_id:'.$input_id.'/" + "input_crm_display:'.$input_crm_display.'/input_uf_display:'.$input_uf_display.'/input_nome_display:'.$input_nome_display.'/" + "input_cpf_display:'.$input_cpf_display.'/" + Math.random());
    }',false);
?>