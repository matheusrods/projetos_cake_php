<?php
if (!empty($usuarios)) : ?>
    <?php echo $paginator->options(array('update' => '#busca-lista-usuario-cliente')); ?>
    <div>
        <table class="table table-striped tabela_select_responsavel_acao">
            <thead>
            <tr>
                <th></th>
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
            <div id="pagination_usuario_responsavel" class='numbers span6'>
                <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
                <?php echo $this->Paginator->numbers(); ?>
                <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
            </div>
            <div class='counter span6'>
                <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            </div>
        </div>

        <input type="hidden" id="usuarioSelecionadoAcao">

        <div class="form-actions">
            <button class="btn btn-primary" id="addSelecionadosIncluir">Salvar</button>
        </div>
    </div>

<?php else: ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>

<?php
echo $this->Javascript->codeBlock(" ");
?>

<script>
    $(function(){

        $("#pagination_usuario_responsavel a").on("click", function(e){
            e.preventDefault();

            var href = $(this).attr("href");

            var div = jQuery("#busca-lista-usuario-cliente");
            bloquearDiv(div);//Bloqueia div da listagem de usuarios e atualiza a listagem
            div.load(href);
            return;
        });

        $(".checkbox").on("change", function(){

            $('.tabela_select_responsavel_acao tbody tr input:checkbox').not(this).removeAttr('checked');

            $("#usuarioSelecionadoAcao").val($(this).attr("data-codigo"))
        })

        $("#addSelecionadosIncluir").on("click", function(){

            var div = jQuery("div#dialogResponsavel");
            bloquearDiv(div);

            var codigo_usuario = $("#usuarioSelecionadoAcao").val();
            var obj_responsavel = [];
            var qtd_acoes_selecionadas = 0;

            if (codigo_usuario.length <= 0) {
                alert("Selecione um usuário para tranferir.")
                desbloquearDiv(div);
                return false;
            }

            $(".tabela_select_responsavel tbody tr input:checkbox").each(function(){

                if ($(this).is(":checked")) {
                    qtd_acoes_selecionadas++;
                    var obj = {
                        codigo_acao_melhoria: $(this).attr("data-codigo"),
                        codigo_usuario_solicitado: codigo_usuario
                    }
                    obj_responsavel.push(obj);
                }
            });

            if (qtd_acoes_selecionadas == 0) {
                alert("Selecione ao menos uma ação para tranfeirir.")
                $( "#dialogResponsavel" ).dialog( "close" );
                desbloquearDiv(div);
                return false;
            }

            var dados = {
                dados : obj_responsavel
            }

            $.ajax({
                type: "POST",
                url: baseUrl + "usuarios/incluir_usuario_responsavel_acao_melhoria",
                data: dados,
                dataType: "json",
                success: function(data) {

                    if (data == 1) {

                        $( "#dialogResponsavel" ).dialog( "close" );
                        desbloquearDiv(div);//Desbloqueia div do modal

                        var codigo_cliente = '<?= $codigo_cliente ?>';

                        var div2 = jQuery(".lista");
                        bloquearDiv(div2);//Bloqueia div da listagem de ações e atualiza a listagem
                        div2.load(baseUrl + "clientes/listagem_acoes_cadastradas_visualizar/" + codigo_cliente + "/" + Math.random());
                    } else {
                        console.log("Erro na requisição");
                        desbloquearDiv(div);
                    }
                },
                error: function(data){
                    console.log("Errou feio");
                    desbloquearDiv(div);
                }
            });
            return;
        })
    })

</script>
