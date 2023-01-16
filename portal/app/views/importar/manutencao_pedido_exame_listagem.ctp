
<?php if(!empty($pedidos_exame)):?>

    <div class='well'>
        <strong>Código: </strong><?php echo $this->Html->tag('span', $funcionario['Funcionario']['codigo']); ?>
        <strong>Nome Funcionário: </strong><?php echo $this->Html->tag('span', $funcionario['Funcionario']['nome']); ?>
    </div>

    <?php echo $this->BForm->hidden('codigo_funcionario', array('value' => $funcionario['Funcionario']['codigo'])); ?>

    <table class='table table-striped tablesorter'>
        <thead>
            <tr>
                <th>Cód. Pedidos Exames</th>
                <th>Cód. Matricula</th>
                <th>Cliente</th>
                <th>Setor</th>
                <th>Cargo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos_exame as $dado): ?>
                <tr>
                    <td><?php echo $dado['0']['codigo_pedido'] ?></td>
                    <td><?php echo $dado['0']['codigo_matricula'] ?></td>
                    <td><?php echo $dado['0']['cliente'] ?></td>
                    <td><?php echo $dado['0']['setor'] ?></td>
                    <td><?php echo $dado['0']['cargo'] ?></td>
                    <td style="min-width: 60px;">                        
                        
                        <a href="javascript:void(0);" onclick="editar_realizacao_datas('<?php echo $dado['0']['codigo_pedido']; ?>', 1);"><i class="icon-edit" title="Atualizar o Pedido de Exame"></i></a>
                        <?php if($dado['0']['pedido_importado'] == 1): ?>
                            <a href="javascript:void(0);" onclick="excluir('<?php echo $dado['0']['codigo_pedido']; ?>', 1);"><i class="icon-trash" title="Excluir Pedido de Exame"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?> 
        </tbody>        
    </table>

    <div class="modal fade" id="modal_realizacao" data-backdrop="static"></div>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('

    jQuery(document).ready(function() {
        setup_mascaras(); setup_time(); setup_datepicker();
        $(".modal").css("z-index", "-1");
        $(".modal").css("width", "43%");

        excluir = function(codigo_pedido)
        {
            swal({
                type: "warning",
                title: "Atenção",
                text: "Tem certeza que deseja excluir este pedido de exame?",
                showCancelButton: true,
                cancelButtonText: "Cancelar",
                confirmButtonText: "Excluir",
                showLoaderOnConfirm: true
            }, function(){
                $.ajax({
                    url: baseUrl + "importar/manutencao_excluir_pedido",
                    type: "POST",
                    dataType: "json",
                    data: {codigo_pedido: codigo_pedido},
                })
                .done(function(response) {
                    if(response) {
                        $("#ImportarFiltrarForm").submit();
                        // location.reload();
                        ///portal/filtros/filtrar/model:ImportacaoPedidosExame/element_name:manutencao_pedido_exame/codigo_cliente:20
                    }
                });
            });
        }//fim excluir

    });

    function editar_realizacao_datas(codigo_pedido,mostra) {
        
        if(mostra) {

            var codigo_funcionario = $("#codigo_funcionario").val();
            
            var div = jQuery("div#modal_realizacao");
            bloquearDiv(div);
            div.load(baseUrl + "importar/modal_manutencao_pedido_exame/" + codigo_pedido + "/" + codigo_funcionario + "/" + Math.random());
    
            $("#modal_realizacao").css("z-index", "1050");
            $("#modal_realizacao").modal("show");

        } else {
            $(".modal").css("z-index", "-1");
            $("#modal_realizacao").modal("hide");
        }

    }


');?>
