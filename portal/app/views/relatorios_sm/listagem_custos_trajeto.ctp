<?php echo $paginator->options(array('update' => 'div.lista')) ?>
<div class="well">
    <?php if(!empty($cliente)): ?>
        <strong>Código: </strong><?= $cliente['cliente']['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['cliente']['Cliente']['razao_social'] ?>
    <?php endif; ?>
    <span class="pull-right">
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>          
    </span>
</div>
<table class="table table-striped" style='width:2000px;max-width:none'>
    <thead>
        <tr>
            <th colspan='2' class="input-small"></th>
            <th colspan='3'><center>Previsto</center></th>
            <th colspan='3'><center>Realizado</center></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        <tr>
            <th class="input-small">SM</th>
            <th class="input-small">Pedido Cliente</th>
            <th class="input-small numeric">Distância (Km)</th>
            <th class="input-small numeric">Consumo (Lts)</th>
            <th class="input-small numeric">Pedágio (R$)</th>
            <th class="input-small numeric">Distância (Km)</th>
            <th class="input-small numeric">Consumo (Lts)</th>
            <th class="input-small numeric">Pedágio (R$)</th>
            <th class="input-small">Placa</th>
            <th class='input-xlarge'>Origem</th>
            <th class='input-xlarge'>Destino</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($relatorio as $viagem): ?>
        <?php $inicioReal = AppModel::dbDateToDate($viagem[0]['InicioReal']); ?>
        <?php $fimReal = empty($viagem[0]['FimReal']) ? date('d/m/Y H:i:s') : AppModel::dbDateToDate($viagem[0]['FimReal']); ?>
        <tr>
            <td class="input-small"><?= $this->Buonny->codigo_sm($viagem[0]['SM']) ?></td>
            <td class="input-small"><?= $viagem[0]['PedidoCliente'] ?></td>
            <td class="input-small numeric"><?= $this->Buonny->moeda($viagem[0]['PrevisaoDistancia'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
            <td class="input-small numeric"><?= $this->Buonny->moeda($viagem[0]['PrevisaoLitrosCombustivel'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
            <td class="input-small numeric"><?= $this->Buonny->moeda($viagem[0]['PrevisaoValorPedagio'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
            <td class="input-small numeric"><?= $this->Buonny->moeda($viagem[0]['DistanciaPercorrida'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
            <td class="input-small numeric"><?= $this->Buonny->moeda($viagem[0]['LitrosCombustivel'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
            <td class="input-small numeric"><?= $this->Buonny->moeda($viagem[0]['ValorPedagio'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
            <td><?= !empty($viagem[0]['Placa']) ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $viagem[0]['Placa']), $inicioReal, $fimReal) : $viagem[0]['Chassi'] ?></td>
            <td><?= $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $viagem[0]['AlvoOrigem']), $viagem[0]['AlvoOrigemLatitude'], $viagem[0]['AlvoOrigemLongitude']) ?></td>
            <td><?= $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $viagem[0]['AlvoDestino']), $viagem[0]['AlvoDestinoLatitude'], $viagem[0]['AlvoDestinoLongitude']) ?></td>
        </tr>
        <?php endforeach ?>
        <tfoot>
            <tr>
                <th class="input-mini">Total: <?= $this->Paginator->counter('{:count}') ?></th>
                <th></th>
                <td class="input-small numeric"><?= $this->Buonny->moeda($totais['PrevisaoDistancia'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
                <td class="input-small numeric"><?= $this->Buonny->moeda($totais['PrevisaoLitrosCombustivel'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
                <td class="input-small numeric"><?= $this->Buonny->moeda($totais['PrevisaoValorPedagio'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
                <td class="input-small numeric"><?= $this->Buonny->moeda($totais['DistanciaPercorrida'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
                <td class="input-small numeric"><?= $this->Buonny->moeda($totais['LitrosCombustivel'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
                <td class="input-small numeric"><?= $this->Buonny->moeda($totais['ValorPedagio'], array('nozero' => false, 'places' => 2, 'nozero' => 1)) ?></td>
                <th></th>
                <th></th>
                <th></th>
            </tr> 
        </tfoot>   
    </tbody>
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
        <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')) ?>
        <?php echo $this->Paginator->numbers() ?>
        <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')) ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')) ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer() ?>