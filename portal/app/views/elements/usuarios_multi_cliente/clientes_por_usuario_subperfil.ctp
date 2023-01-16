<div>
    <div class='actionbar-right' style="margin-bottom: 10px">
        <?php echo $this->Html->link('<i class="icon-plus icon-white"></i>',
            array('controller' => 'usuarios_multi_cliente', 'action' => 'buscar_cliente_usuario_subperfil', $codigo_usuario),
            array('escape' => false, 'class' => 'btn btn-success dialog_cliente_usuario', 'title' =>'Vincular Novos Clientes'));?>
    </div>

    <div id="cliente-usuario-lista"></div>

    <div class="form-actions ">
        <button class="btn btn-warning pull-right" id="removerTodosReceive" style="margin-left: 5px;">Excluir todos</button>
        <button class="btn btn-primary pull-right" id="removeSelecionadosReceive" >Excluir selecionados</button>
    </div>
</div>

<hr/>

<?php echo $this->Javascript->codeBlock("
    $(document).ready(function(){
        setup_time();
        setup_mascaras();
        atualizaLista();
    });

    function atualizaLista(){
        var div = jQuery('#cliente-usuario-lista');
        bloquearDiv(div);
        div.load(baseUrl + 'usuarios_multi_cliente/listagem_subperfil/".$codigo_usuario."/' + Math.random());
    }
")
?>

<script>

    $(function(){

        $("#removeSelecionadosReceive").on("click", function(e){

            e.preventDefault();
            $('#clientes_selecionados_subperfil_receive table tbody tr input:checkbox').each(function(){

                if ($(this).is(":checked")) {
                    $(this).closest("tr").remove();
                }
            });
        });

        $("#removerTodosReceive").on('click', function(e){

            e.preventDefault();
            $('#clientes_selecionados_subperfil_receive table tbody tr').remove();
        })

    })
</script>
