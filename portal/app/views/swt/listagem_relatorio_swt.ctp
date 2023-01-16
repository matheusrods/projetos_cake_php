<?php if (isset($dados_respondido) && count($dados_respondido)) : ?>
    <?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
    ?>

    <div class="well">
        <a id="export_swt" href="javascript:void(0)" title="Exportar para Excel" style="float:right">
            <i class="cus-page-white-excel"></i>
        </a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php echo 'Cód. Cliente' ?></th>
                <th><?php echo 'Razão Social' ?></th>
                <th><?php echo 'Nome Fantasia' ?></th>
                <th><?php echo 'Setor' ?></th>
                <th><?php echo 'Opco' ?></th>
                <th><?php echo 'Business Unit' ?></th>
                <th><?php echo 'ID Walk & Talk' ?></th>
                <th><?php echo 'Observador' ?></th>
                <th><?php echo 'Facilitador' ?></th>
                <th><?php echo 'Data' ?></th>
                <th><?php echo 'Hora' ?></th>
                <th><?php echo 'Descrição Atividade' ?></th>
                <th><?php echo 'Descrição' ?></th>
                <th><?php echo 'Índice de Percepção' ?></th>
                <th><?php echo 'Participantes' ?></th>
                <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($dados_respondido as $dados) : ?>
                <tr>
                    <td><?php echo $dados['Cliente']['codigo'] ?></td>
                    <td><?php echo $dados['Cliente']['razao_social'] ?></td>
                    <td><?php echo $dados['Cliente']['nome_fantasia'] ?></td>
                    <td><?php echo $dados['Setor']['descricao'] ?></td>
                    <td><?php echo $dados['ClienteOpco']['descricao'] ?></td>
                    <td><?php echo $dados['ClienteBu']['descricao'] ?></td>
                    <td>
                        <a href="#void" id="expandir_1" onclick="respostas_formulario(<?php echo $dados['PosSwtFormRespondido']['codigo']; ?>,'1');" title='Dados Questionário Walk & Talk'>
                            <?php echo $dados['PosSwtFormRespondido']['codigo'] ?>&nbsp;<i id="icone" class="icon-search"></i>
                        </a>
                    </td>
                    <td><?php echo $dados['Usuario']['nome'] ?></td>
                    <td><?php echo $dados['UsuarioFacilitador']['nome'] ?></td>
                    <td><?php echo Comum::formataData($dados['PosSwtFormResumo']['data_obs'], 'ymd', 'dmy'); ?></td>
                    <td><?php echo $dados['PosSwtFormResumo']['hora_obs'] ?></td>
                    <td><?php echo $dados['PosSwtFormResumo']['desc_atividade'] ?></td>
                    <td><?php echo $dados['PosSwtFormResumo']['descricao'] ?></td>
                    <td><?php echo $dados['PosSwtFormRespondido']['resultado'] ?></td>
                    <td><?php echo $dados['PosSwtFormParticipantes']['participantes'] ?></td>
                    <td>
                        <a href="#void" id="expandir_1" onclick="respostas_acoes_melhoria(<?php echo $dados['PosSwtFormRespondido']['codigo']; ?>,'1');" title='Dados Ações Melhoria Walk & Talk'><i id="icone_1" class="icon-eye-open"></i></a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="15"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['PosSwtFormRespondido']['count']; ?></td>
            </tr>
        </tfoot>
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        </div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>

    <div class="modal fade " style="width:900px; left: 37%;" id="modal_respondido" data-backdrop="static"></div>
    <div class="modal fade " style="width:900px; left: 37%;" id="modal_acao_melhoria" data-backdrop="static"></div>

<?php else : ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock('
    function atualizaLista(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "swt/listagem_relatorio_swt/" + Math.random());
    }   

    jQuery(document).ready(function(){
        setup_mascaras();

       respostas_formulario = function(codigo_respondido,mostra) {
            if(mostra) {
                var div = jQuery("div#modal_respondido");
                bloquearDiv(div);
                div.load(baseUrl + "swt/modal_respondido/" + codigo_respondido + "/" + Math.random());
        
                $("#modal_respondido").css("z-index", "1050");
                $("#modal_respondido").modal("show");

            } else {
                $(".modal").css("z-index", "-1");
                $("#modal_respondido").modal("hide");
            }
        }//FIM respostas_formulario

        respostas_acoes_melhoria = function(codigo_respondido,mostra) {
            if(mostra) {
                var div = jQuery("div#modal_acao_melhoria");
                bloquearDiv(div);
                div.load(baseUrl + "swt/modal_acao_melhoria/" + codigo_respondido + "/" + Math.random());
        
                $("#modal_acao_melhoria").css("z-index", "1050");
                $("#modal_acao_melhoria").modal("show");

            } else {
                $(".modal").css("z-index", "-1");
                $("#modal_acao_melhoria").modal("hide");
            }
        }//FIM respostas_acoes_melhoria
    });

'); ?>

<style>
    .loadOverlay {
        width: 100%;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);

        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        position: fixed;
        top: 0;
        left: 0;
    }

    .overlayText {
        font-size: 16px;
        color: #fff;

        margin-top: 20px;
    }
</style>


<div class="loadOverlay" style="display: none">
    <?= $this->Html->image('load-gear.gif', array('width' => '50px', 'alt' => 'Loading...')); ?>
    <span class="overlayText"></span>
</div>

<script>
    $(function() {
        $("#export_swt").click(function() {

            swal({
                    title: "Exportar para Excel?",
                    type: "info",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Sim",
                    cancelButtonText: "Não",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        // sim para imprimir os setores e cargos
                        var getUrl = baseUrl + "Swt/url_relatorio/relatorio_swt";

                        var overlay = document.querySelector('.loadOverlay');
                        var overlayText = document.querySelector('.overlayText');

                        overlay.style.display = 'flex';
                        overlayText.innerHTML = 'Aguarde enquanto buscamos suas informações';

                        fetch(getUrl)
                            .then(function(response) {
                                return response.text();
                            })
                            .then(function(text) {
                                var data = JSON.parse(text);

                                if (data.status) {
                                    window.location.href = data.url;

                                    overlayText.innerHTML = 'Tudo pronto para seu dowload';

                                    setTimeout(() => {
                                        overlay.style.display = 'none';
                                    }, 2000);
                                } else {
                                    overlayText.innerHTML = 'Download não disponível. Tente novamente mais tarde';

                                    setTimeout(() => {
                                        overlay.style.display = 'none';
                                    }, 2000);
                                }
                            });
                    } else {
                        return;
                    }
                });
        });
    })
</script>