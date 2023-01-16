<?php if (!empty($registros)) : ?>
    <?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
    ?>

    <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array('controller' => $this->name, 'action' => $this->action, 'destino', 'export'), array('escape' => false, 'title' => 'Exportar para Excel', 'style' => 'float:right')); ?>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Código Cliente</th>
                <th>Razão Social</th>
                <th>Nome Fantasia</th>
                <th>Setor</th>
                <th>Opco</th>
                <th>Business Unit</th>
                <th>ID Observação</th>
                <th>Tipo de Observação</th>
                <th>Observador</th>
                <th>Local da Observação</th>
                <th>Data </th>
                <th>Hora</th>
                <th>Descrição</th>
                <th>O que eu observei</th>
                <th>O que eu fiz a respeito</th>
                <th>Ação Complementar Sugerida</th>
                <th>Fotos da Observação</th>
                <th>Riscos e Impactos</th>
                <th>Status da Observação</th>
                <!-- <th>Ações</th> -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $dados) : ?>
                <tr>
                    <td> <?= $dados['Cliente']['codigo'] ?> </td>
                    <td> <?= $dados['Cliente']['razao_social'] ?> </td>
                    <td> <?= $dados['Cliente']['nome_fantasia'] ?> </td>
                    <td> <?= $dados['Setor']['descricao'] ?> </td>
                    <td> <?= $dados['ClienteOpco']['descricao'] ?> </td>
                    <td> <?= $dados['ClienteBu']['descricao'] ?> </td>
                    <td> <?= $dados['PosObsObservacoes']['codigo'] ?> </td>
                    <td> <?= $dados['Categoria']['descricao'] ?> </td>
                    <td> <?= $dados['Usuario']['nome'] ?> </td>
                    <td> <?= $dados['Local']['descricao'] ?: $dados['Cliente']['nome_fantasia'] ?> </td>
                    <td> <?= Comum::formataData($dados[0]['dt_obs'], 'ymd', 'dmy') ?> </td>
                    <td> <?= substr($dados[0]['hr_obs'], 0, 5) ?> </td>
                    <td> <?= $dados['PosObsObservacoes']['descricao'] ?> </td>
                    <td> <?= $dados['PosObsObservacoes']['descricao_usuario_observou'] ?> </td>
                    <td> <?= $dados['PosObsObservacoes']['descricao_usuario_acao'] ?> </td>
                    <td> <?= $dados['PosObsObservacoes']['descricao_usuario_sugestao'] ?> </td>
                    <td>
                        <a href="#void" id="expandir_fotos" onclick="fotos(<?php echo $dados['PosObsObservacoes']['codigo']; ?>,'1');" title='Fotos da Observação'><i id="icone_fotos" class="icon-eye-open"></i>
                    </td>
                    <td>
                        <a href="#void" id="expandir_riscos" onclick="riscos(<?php echo $dados['PosObsObservacoes']['codigo']; ?>,'1');" title='Riscos da Observação'><i id="icone_risco" class="icon-eye-open"></i>
                    </td>
                    <td> <?= $dados['AcoesMelhoriasStatus']['descricao'] ?> </td>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="19"><strong>Total</strong> <?= @$this->Paginator->params['paging']['count']; ?></td>
            </tr>
        </tfoot>
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?= $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?= $this->Paginator->numbers(); ?>
            <?= $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?= $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>

        </div>
    </div>

    <div class="modal fade " style="width:900px; left: 37%;" id="modal_fotos" data-backdrop="static"></div>
    <div class="modal fade " style="width:900px; left: 37%;" id="modal_riscos" data-backdrop="static"></div>

    <?= $this->Js->writeBuffer(); ?>
    <?= $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();

       fotos = function(codigo_obs,mostra) {
            if(mostra) {
                var div = jQuery("div#modal_fotos");
                bloquearDiv(div);
                div.load(baseUrl + "pos_obs_relatorio_realizadas/modal_fotos/" + codigo_obs + "/" + Math.random());
        
                $("#modal_fotos").css("z-index", "1050");
                $("#modal_fotos").modal("show");

            } else {
                $(".modal").css("z-index", "-1");
                $("#modal_fotos").modal("hide");
            }
        }//FIM fotos

        riscos = function(codigo_obs,mostra) {
            if(mostra) {
                var div = jQuery("div#modal_riscos");
                bloquearDiv(div);
                div.load(baseUrl + "pos_obs_relatorio_realizadas/modal_riscos/" + codigo_obs + "/" + Math.random());
        
                $("#modal_riscos").css("z-index", "1050");
                $("#modal_riscos").modal("show");

            } else {
                $(".modal").css("z-index", "-1");
                $("#modal_riscos").modal("hide");
            }
        }//FIM riscos

        
    });
    ');
    ?>
<?php else : ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>