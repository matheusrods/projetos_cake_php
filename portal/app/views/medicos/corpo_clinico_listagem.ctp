<?php
    echo $paginator->options(array('update' => 'div.lista'));
    // $total_paginas = $this->Paginator->numbers();
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-small">Medico</th>
            <th class="input-medium">Conselho Profissional</th>
            <th class="input-medium">Conselho Nº</th>
            <th class="input-medium">Conselho UF</th>
            <th class="input-medium">Data</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $d): ?>
        <tr>
            <td><?= $d[0]['medico'] ?></td>
            <td><?= $d[0]['conselho_profissional'] ?></td>
            <td><?= $d[0]['conselho_numero'] ?></td>
            <td><?= (!empty($d[0]['conselho_uf']) ? $d[0]['conselho_uf'] : '-') ?></td>
            <td><?= date("d/m/Y", strtotime($d[0]['incluido'])) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['FornecedorMedico']['count']; ?></td>
        </tr>
    </tfoot>
</table>

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