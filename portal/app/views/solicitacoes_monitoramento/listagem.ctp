<?php if ($this->passedArgs[0] != 'export'): ?>
    <div class='well'>
        <span class="pull-right">
            <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>   
        </span>
    </div>

    <?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
    ?>

    <div class='row-fluid' style='overflow-x:auto'>
        <table class='table table-striped horizontal-scroll' style = 'width:4500' > 
            <thead >
                <tr>
                    <th class='input-small'  title="Código SM"><?= $this->Paginator->sort('SM', 'Recebsm.SM') ?></th>
                    <th class='input-medium' title="Placa"><?= $this->Paginator->sort('Placa', 'Recebsm.Placa') ?></th>
                    <th class='input-medium' title="Início"><?= $this->Paginator->sort('Início', 'Recebsm.data_inicio') ?></th>
                    <th class='input-medium' title="Fim"><?= $this->Paginator->sort('Fim', 'Recebsm.a_data_final') ?></th>
                    <th class='input-xxlarge' title="Transportador"><?= $this->Paginator->sort('Transportador', 'TPjurTransportador.pjur_razao_social') ?></th>
                    <th class='input-xxlarge' title="Embarcador"><?= $this->Paginator->sort('Embarcador', 'TPjurEmbarcador.pjur_razao_social') ?></th>
                    <th class='input-xxlarge' title="Gerenciadora"><?= $this->Paginator->sort('Gerenciadora', 'Recebsm.nome_gerenciadora') ?></th>
                    <th class='input-large' title="Operação"><?= $this->Paginator->sort('Operação', 'Operacao.descricao') ?></th>
                    <th class='input-large' title="Estação"><?= $this->Paginator->sort('Estação', 'TErasEstacaoRastreamento.eras_descricao') ?></th>
                    <th class='input-large' title="Tecnologia"><?= $this->Paginator->sort('Tecnologia', 'Equipamento.descricao') ?></th>
                    <th class='input-large' title="Número Terminal"><?= $this->Paginator->sort('Terminal', 'TTermTerminal.term_numero_terminal') ?></th>
                    <th class='input-medium' title="Previsão de Inicio"><?= $this->Paginator->sort('Previsão de Inicio', 'Recebsm.data_previsao_inicio') ?></th>
                    <th class='input-medium' title="Previsão de Fim"><?= $this->Paginator->sort('Previsão de Fim', 'Recebsm.data_previsao_fim') ?></th>
                    <th class='input-xlarge' title="Cidade Origem"><?= $this->Paginator->sort('Cidade Origem', 'CidadeOrigem.descricao') ?></th>
                    <th class='input-small' title="Estado Origem"><?= $this->Paginator->sort('Estado Origem', 'CidadeOrigem.estado') ?></th>
                    <th class='input-xlarge' title="Cidade Destino"><?= $this->Paginator->sort('Cidade Destino', 'CidadeDestino.descricao') ?></th>
                    <th class='input-small' title="Estado Destino"><?= $this->Paginator->sort('Estado Destino', 'CidadeDestino.estado') ?></th>
                    <th class='input-xlarge' title="Nome"><?= $this->Paginator->sort('Nome Motorista', 'Motorista.nome') ?></th>
                    <th class='input-medium' title="CPF"><?= $this->Paginator->sort('CPF Motorista', 'Motorista.cpf') ?></th>
                    <th style="text-align: right;" class='input-small' title="Valor SM"><?= $this->Paginator->sort('Valor SM', 'Recebsm.ValSM') ?></th>

                </tr>

            </thead>
            <tbody >
                <?php foreach ($solicitacoes_monitoramento as $solicitacao_monitoramento) : ;?>
                <tr>
                    <td><?= $this->Buonny->codigo_sm($solicitacao_monitoramento['0']['SM']); ?></td>
                    <td><?= $solicitacao_monitoramento['0']['Placa']?></td>
                    <td><?= $solicitacao_monitoramento['0']['data_inicial'].' '.$solicitacao_monitoramento['0']['hora_inicial']?></td>
                    <td><?= $solicitacao_monitoramento['0']['data_final'].' '.$solicitacao_monitoramento['0']['hora_final']?></td>
                    <td><?= $solicitacao_monitoramento['Transportador']['Raz_Social'] ?></td>
                    <td><?= $solicitacao_monitoramento['Embarcador']['Raz_Social'] ?></td>
                    <td><?= $solicitacao_monitoramento['Recebsm']['nome_gerenciadora'] ?></td>
                    <td><?= $solicitacao_monitoramento['Operacao']['descricao'] ?></td>
                    <td><?= (isset($solicitacao_monitoramento['TErasEstacaoRastreamento']['eras_descricao']))?$solicitacao_monitoramento['TErasEstacaoRastreamento']['eras_descricao']:NULL ?></td>
                    <td><?= $solicitacao_monitoramento['Equipamento']['descricao'] ?></td>
                    <td><?= (isset($solicitacao_monitoramento['TTermTerminal']['term_numero_terminal']))?$solicitacao_monitoramento['TTermTerminal']['term_numero_terminal']:NULL ?></td>
                    <td><?= $solicitacao_monitoramento['0']['data_previsao_inicio'].' '.$solicitacao_monitoramento['Recebsm']['Hora_Inc'] ?></td>
                    <td><?= $solicitacao_monitoramento['0']['data_previsao_fim'].' '.$solicitacao_monitoramento['Recebsm']['Hora_Fim'] ?></td>
                    <td><?= $solicitacao_monitoramento['CidadeOrigem']['descricao'] ?></td>
                    <td><?= $solicitacao_monitoramento['CidadeOrigem']['estado'] ?></td>
                    <td><?= $solicitacao_monitoramento['CidadeDestino']['descricao'] ?></td>
                    <td><?= $solicitacao_monitoramento['CidadeDestino']['estado'] ?></td>
                    <td><?= $solicitacao_monitoramento['Motorista']['Nome'] ?></td>
                    <td><?= $solicitacao_monitoramento['Motorista']['CPF'] ?></td>
                    <td style="text-align: right;" ><?= $this->Buonny->moeda($solicitacao_monitoramento['Recebsm']['ValSM']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div>
    <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
    <?php echo $this->Paginator->numbers(); ?>
    <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
</div>

<?php
$total_sms = isset($this->Paginator->params['paging']['Recebsm']['count']) ? $this->Paginator->params['paging']['Recebsm']['count']: 0;
$total_paginas = isset($this->Paginator->params['paging']['Recebsm']['pageCount']) ? $this->Paginator->params['paging']['Recebsm']['pageCount']: 0;

echo $this->Paginator->counter(array('format' => 'Página %page% de '.preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/",".",$total_paginas).', mostrando %current% registros de um total de ' . preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/",".",$total_sms) ));
?>
<br />
<br />
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?= $this->Js->writeBuffer(); ?>
<?php echo $this->Buonny->link_css('jquery.tablescroll'); ?>
<?php echo $this->Buonny->link_js('jquery.tablescroll'); ?>
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
            $('.horizontal-scroll').tableScroll({width:4500,height:500});
        });
");
?>
<?php echo $this->Js->writeBuffer(); ?>
<?php endif; ?>