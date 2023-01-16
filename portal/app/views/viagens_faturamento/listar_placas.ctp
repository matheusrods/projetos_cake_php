<?php 
$valor_total = 0;
$linha = null;
?>

<div class='well'>
    <strong>Código: </strong><?= $pagador['Cliente']['codigo'] ?>
    <strong>Cliente: </strong><?= $pagador['Cliente']['razao_social'] ?>
</div>

<table class="table table-striped table-bordered">
    <thead>
        <tr >
            <th>Placa</th>
            <th class='numeric'>Valor do Serviço (R$)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($placas as $placa): ?>
            <?php $valor_total += ($valor_servico ? $valor_servico[0]['ClienteProdutoServico2']['valor'] : 0) ?>
            <tr >
                <td><?= $placa ?></td>
                <td class='numeric'><?= $this->Buonny->moeda(($valor_servico ? $valor_servico[0]['ClienteProdutoServico2']['valor'] : 0)) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <td class='numeric'><?= count($placas) ?></td>
        <td class='numeric'><?= $this->Buonny->moeda($valor_total) ?></td>
    </tfoot>
</table>