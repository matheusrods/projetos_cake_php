<div class='well'>   
    <?php echo $bajax->form('UsuarioUnidade', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'UsuarioUnidade', 'element_name' => 'buscar_usuario_unidade', 'codigo_usuario' => $codigo_usuario), 'divupdate' => '.form-procurar-cliente')) ?>
        <?php echo $this->element('usuarios_multi_cliente/fields_filtros') ?>
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
            bloquearDiv(jQuery(".form-procurar-cliente"));
            jQuery(".form-procurar-cliente").load(baseUrl + "/filtros/limpar/model:UsuarioUnidade/element_name:buscar_usuario_unidade/codigo_usuario:'.$codigo_usuario.'/" + Math.random())
        });
        atualizaLista("'.$codigo_usuario.'");
    });

    function atualizaLista(codigo_usuario) {
        var div = jQuery("div#busca-lista");
        bloquearDiv(div);
        div.load(baseUrl + "usuarios/buscar_listagem_usuario_unidade/"+ codigo_usuario+"/" + Math.random());

    }',false);
?>

