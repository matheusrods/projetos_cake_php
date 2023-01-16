<div class='well'>   
    <?php echo $bajax->form('Cid', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cid', 'element_name' => 'buscar_cid', 'codigo_atestado' => $codigo_atestado), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('cid/fields_filtros') ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cid/element_name:buscar_cid/codigo_atestado:'.$codigo_atestado.'/" + Math.random())
        });
        atualizaLista("buscar_cid", "'.$codigo_atestado.'");
    });

    function atualizaLista(destino, codigo_atestado) {
        var div = jQuery("div#busca-lista");
        bloquearDiv(div);
        div.load(baseUrl + "cid/buscar_listagem/" + destino.toLowerCase() + "/" + codigo_atestado + "/" + Math.random());
    }',false);
?>

