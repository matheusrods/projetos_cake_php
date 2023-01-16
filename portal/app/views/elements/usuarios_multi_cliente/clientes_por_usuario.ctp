<div>
    <div class='actionbar-right' style='margin-bottom: 10px;  min-height: 50px'>
        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'usuarios_multi_cliente', 'action' => 'buscar_cliente_usuario', $codigo_usuario), array('escape' => false, 'class' => 'btn btn-success dialog_cliente_usuario', 'title' =>'Vincular Novos Clientes'));?>
    </div>

    <div id="cliente-usuario-lista"></div>
</div>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
        atualizaLista();
    });

    function atualizaLista(){
        var div = jQuery('#cliente-usuario-lista');
        bloquearDiv(div);
        div.load(baseUrl + 'usuarios_multi_cliente/listagem/".$codigo_usuario."/' + Math.random());
    }
")
?>
