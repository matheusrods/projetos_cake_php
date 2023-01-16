<div class='well'>   
    <?php echo $bajax->form('Medico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Medico', 'element_name' => 'buscar_medico', 'codigo_fornecedor' => $codigo_fornecedor), 'divupdate' => '.form-procurar')) ?>
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
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Medico/element_name:buscar_medico/codigo_fornecedor:'.$codigo_fornecedor.'/" + Math.random())
        });
        atualizaLista("buscar_medico", "'.$codigo_fornecedor.'");
    });


    function atualizaLista(destino, codigo_fornecedor) {
        var div = jQuery("div#busca-lista");
        bloquearDiv(div);
        div.load(baseUrl + "medicos/buscar_listagem_readonly/" + destino.toLowerCase() + "/" + codigo_fornecedor + "/" + Math.random());
    }',false);
?>

