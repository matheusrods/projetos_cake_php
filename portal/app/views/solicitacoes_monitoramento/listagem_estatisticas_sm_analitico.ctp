<div class='row-fluid inline'>
    <?php if(isset($dados) && count($dados) > 0): ?>
        <?php echo $this->Paginator->options(array('update' => '.lista')); ?>
        <table class='table table-striped tablesorter'>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('SM', 'Recebsm.SM') ?></th>
                    <th><?= $this->Paginator->sort('Pagador', 'Recebsm.pagador') ?></th>
                    <th><?= $this->Paginator->sort('Embarcador', 'Recebsm.embarcador') ?></th>
                    <th><?= $this->Paginator->sort('Transportador', 'Recebsm.transportador') ?></th>
                    <th><?= $this->Paginator->sort('Valor (R$)', 'Recebsm.valor') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($dados as $dado): ?>
                <tr>
                    <td><?= $this->Buonny->codigo_sm($dado['Recebsm']['SM']) ?></td>
                    <td><?= $dado['Recebsm']['codigo_pagador'].' - '.$dado['Recebsm']['pagador'] ?></td>
                    <td><?= $dado['Recebsm']['codigo_embarcador'].' - '.$dado['Recebsm']['embarcador'] ?></td>
                    <td><?= $dado['Recebsm']['codigo_transportador'].' - '.$dado['Recebsm']['transportador'] ?></td>
                    <td class="numeric"><?= $this->Buonny->moeda($dado['Recebsm']['valor'], array('nozero' => true)) ?></td>
                </tr>
                <?php endforeach; ?>        
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"><strong>Total: </strong><?php echo $this->Paginator->counter(array('format' => '%count%')); ?></td>
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
    <?php else: ?>
        <?php if (isset($dados) && count($dados)==0): ?>
            <div class="alert">Nenhum dado foi encontrado.</div>
        <?php endif ?>
    <?php endif ?>
</div>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter({sortList: [[0,1]], headers: {3: {sorter:false}} })")) ?> 
<?php echo $this->Javascript->codeBlock("
            jQuery(document).ready(function(){
                $('.numbers a[id^=\"link\"]').bind('click', function (event) { bloquearDiv($('.lista')); });
            });", false);
        ?>