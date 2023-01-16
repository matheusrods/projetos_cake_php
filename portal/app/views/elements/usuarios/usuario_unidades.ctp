<div>
    <div class='actionbar-right' style='margin-bottom: 10px;  min-height: 50px'>
        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'usuarios', 'action' => 'buscar_usuario_unidade', $codigo_usuario), array('escape' => false, 'class' => 'btn btn-success dialog_cliente_usuario', 'title' =>'Vincular Unidades'));?>
    </div>

    <div id="usuario_unidades_lista"></div>
</div>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
        atualizaListaUnidades();
    });

    function atualizaListaUnidades(){
        var div = jQuery('#usuario_unidades_lista');
        bloquearDiv(div);
        div.load(baseUrl + 'usuarios/usuarios_unidades_listagem/".$codigo_usuario."/' + Math.random());
    }
")
?>
