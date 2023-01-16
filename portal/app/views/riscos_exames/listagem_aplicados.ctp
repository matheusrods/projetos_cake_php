<?php if (!empty($listagem)) : ?>

    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

    <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array('controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' => 'Exportar para Excel', 'style' => 'float:right')); ?>
    </div>


    <table class="table table-striped" style='width:1800px;max-width:none;'>
        <thead>
            <tr>
                <th>Unidade</th>
                <th>Setor</th>
                <th>Cargo</th>
                <th>Nome Funcionário</th>
                <th>CPF</th>
                <th>Matricula</th>

                <?php if ($tipos_ppra_pcmso == 1) : ?>
                    <th>Exames</th>
                    <th>Aplicável em</th>
                    <th>Periodicidade</th>
                <?php else : ?>
                    <th>Atribuições</th>
                    <th>Riscos</th>
                <?php endif; ?>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($listagem as $dados) : ?>
                <tr>
                    <td><?php echo $dados[0]['unidade_nome_fantasia'] ?></td>
                    <td><?php echo $dados[0]['setor_descricao'] ?></td>
                    <td><?php echo $dados[0]['cargo_descricao'] ?></td>
                    <td><?php echo $dados[0]['funcionario_nome'] ?></td>
                    <td><?php echo $dados[0]['funcionario_cpf'] ?></td>
                    <td><?php echo $dados[0]['funcionario_matricula'] ?></td>

                    <?php if ($tipos_ppra_pcmso == 1) : ?>
                        <td><?php echo $dados[0]['exame_descricao']; ?></td>
                        <td>
                            <?php
                            if ($dados[0]['exame_admissional'] == 1) {
                                echo 'Admissional';
                            }

                            if ($dados[0]['exame_periodico'] == 1) {
                                echo ', Periodico';
                            }

                            if ($dados[0]['exame_demissional'] == 1) {
                                echo ', Demissional';
                            }

                            if ($dados[0]['exame_retorno'] == 1) {
                                echo ', Retorno';
                            }

                            if ($dados[0]['exame_mudanca'] == 1) {
                                echo ', Mudança de Riscos Ocupacionais';
                            }

                            if ($dados[0]['exame_monitoracao'] == 1) {
                                echo ', Monitoração Pontual';
                            }
                            ?>
                        </td>
                        <td>
                            <?php

                            $periodos = '';
                            $periodo_meses = trim($dados[0]['periodo_meses']);
                            if (!empty($periodo_meses)) {
                                $periodos = $periodo_meses;
                            } else {
                                $idade = trim($dados[0]['periodo_idade']);
                                if (!empty($idade)) {
                                    $periodos = 'Idade:' . $idade . ', Meses:' . $dados[0]['qtd_periodo_idade'];
                                }

                                $idade2 = trim($dados[0]['periodo_idade_2']);
                                if (!empty($idade2)) {

                                    if (!empty($periodos)) {
                                        $periodos .= "<br>";
                                    }
                                    $periodos .= 'Idade:' . $idade2 . ', Meses:' . $dados[0]['qtd_periodo_idade_2'];
                                }

                                $idade3 = trim($dados[0]['periodo_idade_3']);
                                if (!empty($idade3)) {

                                    if (!empty($periodos)) {
                                        $periodos .= "<br>";
                                    }
                                    $periodos .= 'Idade:' . $idade3 . ', Meses:' . $dados[0]['qtd_periodo_idade_3'];
                                }

                                $idade4 = trim($dados[0]['periodo_idade_4']);
                                if (!empty($idade4)) {

                                    if (!empty($periodos)) {
                                        $periodos .= "<br>";
                                    }
                                    $periodos .= 'Idade:' . $idade4 . ', Meses' . $dados[0]['qtd_periodo_idade_4'];
                                }
                            }

                            echo $periodos;
                            ?>
                        </td>

                    <?php elseif ($tipos_ppra_pcmso == 2) : ?>

                        <td>
                            <?php
                            if (isset($infoTipos[$dados[0]['codigo_grupo_exposicao']])) {
                                $atribuicoes = array();
                                $dadosAtri = $infoTipos[$dados[0]['codigo_grupo_exposicao']];
                                foreach ($dadosAtri as $val) {
                                    $atribuicoes[] = $val;
                                }
                                echo implode(",", $atribuicoes);
                            }
                            ?>
                        </td>
                        <td><?php echo $dados[0]['risco_descricao']; ?></td>

                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>

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

    <?php //echo $javascript->link('comum.js'); 
    ?>
<?php else : ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>