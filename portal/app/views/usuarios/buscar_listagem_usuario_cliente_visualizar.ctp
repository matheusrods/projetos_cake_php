<?php
if (!empty($usuarios)) : ?>

<div>
    <table class="table table-striped tabela_select_responsavel">
        <thead>
        <tr>
            <th><input type="checkbox" class="responsavel_select_all"></th>
            <th>Código cliente</th>
            <th>Razão social</th>
            <th>Nome fantasia </th>
            <th>Nome</th>
            <th>Perfil</th>
            <th>Área atuação</th>
        </tr>
        </thead>

        <tbody>
        <?php
        foreach ($usuarios as $usuario) { ?>

            <tr>
                <td><input type="checkbox" class="checkbox" data-codigo="<?php echo $usuario['Usuario']['codigo']; ?>"></td>
                <td class="input-mini"><?php echo $usuario['ClientesFuncionarios']['codigo_cliente']; ?></td>
                <td><?php echo $usuario['Cliente']['razao_social']; ?></td>
                <td><?php echo $usuario['Cliente']['nome_fantasia']; ?></td>
                <td><?php echo $usuario['Usuario']['nome']; ?></td>
                <td><?php echo $usuario['Uperfil']['descricao']; ?></td>
                <td><?php echo $usuario[0]['codigo_area_atuacao']; ?></td>
            </tr>
        <?php }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Usuario']['count']; ?></td>
        </tr>
        </tfoot>
    </table>

    <div class='row-fluid'>
        <div id="pagination_usuario_responsavel_visualizar" class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        </div>
    </div>
</div>

<div class="form-actions">
    <button class="btn btn-primary" id="removerSelecionadosAjax">Remover selecionados</button>
    <button class="btn btn-warning" id="removeSelecionadosVisualizar">Resetar</button>
</div>

<?php else: ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>

<script>
    $(function(){

        $("#pagination_usuario_responsavel_visualizar a").on("click", function(e){
            e.preventDefault();

            var href = $(this).attr("href");

            var div = jQuery("#busca-lista-usuario-cliente-visualizar");
            bloquearDiv(div);//Bloqueia div da listagem de usuarios e atualiza a listagem
            div.load(href);
            return;
        });

        $("#removeSelecionadosVisualizar").on("click", function(){
            $('.tabela_select_responsavel tr input:checkbox').removeProp('checked');
        })

        $(".responsavel_select_all").on("change", function(){

            if ($(this).is(":checked")) {
                $('.tabela_select_responsavel tbody tr input:checkbox').prop('checked','checked');
            } else {
                $('.tabela_select_responsavel tbody tr input:checkbox').removeProp('checked');
            }
        })

        $("#removerSelecionadosAjax").on("click", function(){

            var codigo_cliente = <?= $codigo_cliente ?>;
            var obj_responsavel = [];

            $(".tabela_select_responsavel tbody tr input:checkbox").each(function(){

                if ($(this).is(":checked")) {

                    var codigo_usuario = $(this).attr('data-codigo');

                    var obj = {
                        codigo_cliente: codigo_cliente,
                        codigo_usuario: codigo_usuario
                    }
                    obj_responsavel.push(obj);
                }
            });

            if (obj_responsavel.length == 0) {
                alert("Selecione ao menos 1 usuário!");
                return;
            }

            var dados = {
                dados : obj_responsavel
            }

            $.ajax({
                type: "POST",
                url: baseUrl + "usuarios/remover_usuario_cliente",
                data: dados,
                dataType: "json",
                success: function(data) {

                    $( "#dialogResponsavelVisualizar" ).dialog( "close" );

                },
                error: function(data){
                    console.log("Errou feio")
                }
            });
            return;
        })
    })
</script>
