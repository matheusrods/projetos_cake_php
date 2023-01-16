<?php
    echo $this->Paginator->options(array('update' => 'div.lista'));
?>
<div class='well'>
   <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini">Ramal</th>
            <th class="input-medium">Inicio da pesquisa</th>
            <th class="input-medium">Fim da pesquisa</th>
            <th class="input-mini">Telefone Origem</th>
            <th class="input-mini">Tronco</th>
            <th class="input-mini">Status</th>
            <th class="input-mini">Pontuação</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($registros_ura as $registro_ura): ?>
        <tr>
            <td><?php echo (!empty($registro_ura[0]['ramal']) ? $registro_ura[0]['ramal'] : null) ?></td>
            <td><?php echo (!empty($registro_ura[0]['startq']) ? date('d/m/Y H:i:s', strtotime(str_replace('/', '-', ($registro_ura[0]['startq'])))) : null) ?></td>
            <td><?php echo (!empty($registro_ura[0]['endq']) ? date('d/m/Y H:i:s', strtotime(str_replace('/', '-', ($registro_ura[0]['endq'])))) : null) ?></td>
            <td><?php echo (!empty($registro_ura[0]['oani']) ? $registro_ura[0]['oani'] : null) ?></td> 
            <td><?php echo (!empty($registro_ura[0]['otrkid']) ? $registro_ura[0]['otrkid'] : null) ?></td>
            <td><?php echo ($registro_ura[0]['status'] == 0 ? 'Não Avaliado' : 'Avaliada') ?></td>
            <td><?php echo (!empty($registro_ura[0]['score']) ? $registro_ura[0]['score'] : null) ?></td> 
        </tr>
    <?php endforeach ?>
</tbody>
    <tfoot>
        <tr>
            <td colspan = "11"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ModIvrPesquisa']['count']; ?></td>
        </tr>
    </tfoot>    
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
        <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disable paginacao_anterior')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disable paginacao_proximo')); ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>