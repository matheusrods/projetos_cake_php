<div class='well'>   
    <?php echo $bajax->form('ClienteFornecedor', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteFornecedor', 'element_name' => 'buscar_cliente_fornecedor', 'codigo_cliente' => $codigo_cliente), 'divupdate' => '.form-procurar-fornecedor')) ?>
        <?php echo $this->element('clientes_fornecedores/fields_filtros') ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_time();
        setup_mascaras();
        setup_datepicker();

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar-fornecedor"));
            jQuery(".form-procurar-fornecedor").load(baseUrl + "/filtros/limpar/model:ClienteFornecedor/element_name:buscar_cliente_fornecedor/codigo_cliente:'.$codigo_cliente.'/" + Math.random())
        });
        atualizaLista("'.$codigo_cliente.'");
    });


    function atualizaLista(codigo_cliente) {
        var div = jQuery("div#busca-lista");
        bloquearDiv(div);
        div.load(baseUrl + "clientes_fornecedores/buscar_listagem_cliente_fornecedor/" + codigo_cliente + "/" + Math.random());
    }',false);
?>

