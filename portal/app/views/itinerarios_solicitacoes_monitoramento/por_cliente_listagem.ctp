<?php if ($this->passedArgs[0] == 'export'): ?>
    <?php header(sprintf('Content-Disposition: attachment; filename="%s"', basename('acompanhamento_sm.csv'))); ?>
    <?php header('Pragma: no-cache'); ?>
    <?= "SM;Loadplan;Nota Fiscal;Placa;Transportadora;Cliente;Cidade;Estado;Inicio Previsto;Inicio Real;Final Previsto;Final Real;Posicao Atual;Km Restante;Status\n" ?>
    <?php foreach ($sms as $sm): ?>
        <?= $sm['Recebsm']['sm'] . ';' ?>
        <?= $sm['MSmitinerario']['loadplan'] . ';' ?>
        <?= $sm['MSmitinerario']['nf'] . ';' ?>
        <?= $sm['Recebsm']['placa'] . ';' ?>
        <?= $sm['ClientEmpresaTransportador']['raz_social'] . ';' ?>
        <?= $sm['MSmitinerario']['empresa'] . ';' ?>
        <?= $sm['Cidade']['descricao'] . ';' ?>
        <?= $sm['Cidade']['estado'] . ';' ?>
        <?= date('d/m/Y', strtotime($sm['0']['dta_inc'])) ." ". date('H:i:s', strtotime($sm['Recebsm']['hora_inc'])) . ';' ?>
        <?= (!empty($sm['Recebsm']['data_inicio_real']) ? $sm['Recebsm']['data_inicio_real'] : $sm['0']['data_inicio_real_monitora']) . ';' ?>
        <?= date('d/m/Y', strtotime($sm['0']['dta_fim'])) ." ". date('H:i:s', strtotime($sm['Recebsm']['hora_fim'])) . ';' ?>
        <?= (!empty($sm['Recebsm']['data_final_real']) ? $sm['Recebsm']['data_final_real'] : $sm['0']['data_final_real_monitora']) . ';' ?>
        <?= $sm['ViagemLocal']['upos_descricao_sistema'] . ';' ?>
        <?= $sm['distancia'] . ';' ?>
        <?= $sm[0]['status'] . "\n" ?>
    <?php endforeach; ?>
<?php else: ?>
    <?php
        echo $this->Paginator->options(array('update' => 'div.lista')); 
        $total_paginas = $this->Paginator->numbers();
    ?>
    <?php if (isset($cliente)): ?>
    <div class='well'>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>
    <?php endif ?>
    <div class='row-fluid' style='overflow-x:auto'>
        <table class='table table-striped' style='width:2000px;max-width:none;'>
            <thead>
                <th>SM</th>
                <th>Loadplan</th>
                <th>Nota Fiscal</th>
                <th class='input-small'>Placa</th>
                <th>Transportadora</th>
                <th>Cliente</th>
                <th>Cidade</th>
                <th>Estado</th>
                <th>Prev.Início</th>
                <th>Início</th>
                <th>Prev.Chegada</th>
                <th>Chegada</th>
                <th>Posição Atual</th>
                <th class='numeric'>Km Restante</th>
                <th>Status</th>
            </thead>
            <tbody>
                <?php foreach ($sms as $sm): ?>
                    <?php $data_inicial = AppModel::dbDateToDate($sm['0']['dta_inc']) ?> 
                    <?php $data_final = AppModel::dbDateToDate($sm['0']['dta_fim']) ?> 
                    <?php $distancia = (empty($sm['distancia']) ? '' : $this->Buonny->moeda($sm['distancia'])) ?>
                    <?php if (in_array($sm[0]['status'], array('Entregando', 'Entregue')) ) $distancia = '' ?>
                    <tr>             
                        <td><?php echo $this->Buonny->codigo_sm($sm['Recebsm']['sm']);?></td>
                        <td><?php echo $sm['MSmitinerario']['loadplan'] ?></td>
                        <td><?php echo $sm['MSmitinerario']['nf'] ?></td>
                        <td><?php echo $this->Buonny->placa($sm['Recebsm']['placa'], $data_inicial, $data_final) ?></td>
                        <td><?php echo $sm['ClientEmpresaTransportador']['raz_social'] ?></td>
                        <td><?php echo $sm['MSmitinerario']['empresa'] ?></td>
                        <td><?php echo $sm['Cidade']['descricao'] ?></td>
                        <td><?php echo $sm['Cidade']['estado'] ?></td>
                        <td><?php echo AppModel::dbDateToDate(substr($sm['0']['dta_inc'],0,10)) . ' ' . $sm['Recebsm']['hora_inc'] ?></td>
                        <td><?php echo (!empty($sm['Recebsm']['data_inicio_real']) ? $sm['Recebsm']['data_inicio_real'] : $sm['0']['data_inicio_real_monitora']) ?></td>
                        <td><?php echo AppModel::dbDateToDate(substr($sm['0']['dta_fim'],0,10)) . ' ' . $sm['Recebsm']['hora_fim'] ?></td>
                        <td><?php echo (!empty($sm['Recebsm']['data_final_real']) ? $sm['Recebsm']['data_final_real'] : $sm['0']['data_final_real_monitora']) ?></td>
                        <td><?php echo $this->Buonny->posicao_geografica($sm['ViagemLocal']['upos_descricao_sistema'], $sm['ViagemLocal']['upos_latitude'], $sm['ViagemLocal']['upos_longitude']) ?></td>
                        <td class='numeric'><?php $distancia ?></td>
                        <td><?php echo $sm[0]['status'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class='numeric'><?php echo $total_sms; ?></td>
                    <td colspan="14"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class='row-fluid'>
        <div class='numbers span6'>
        	<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
        	<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        </div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>
<?php endif ?>