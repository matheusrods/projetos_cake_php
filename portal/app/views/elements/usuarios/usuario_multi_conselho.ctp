<div>
    <div class='actionbar-right' style='margin-bottom: 10px;  min-height: 50px'>
        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'usuarios', 'action' => 'buscar_usuario_multi_conselho', $codigo_usuario), array('escape' => false, 'class' => 'btn btn-success dialog_cliente_usuario', 'title' =>'Vincular Conselho'));?>
    </div>

    <div id="usuario_multi_conselho"></div>
</div>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
        atualizaListaConselho();
    });

    function atualizaListaConselho(){
        var div = jQuery('#usuario_multi_conselho');
        bloquearDiv(div);
        div.load(baseUrl + 'usuarios/usuario_multi_conselho_listagem/".$codigo_usuario."/' + Math.random());
    }
")
?>
