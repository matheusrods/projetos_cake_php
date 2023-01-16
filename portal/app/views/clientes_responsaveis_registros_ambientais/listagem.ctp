<?php if(!empty($registros_ambientais)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width:100px">Código Cliente</th>
                <th>Cliente</th>
                <th>Nome do Profissional Responsável</th>
                <th class="input-medium">Período</th>
                <th class="acoes">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros_ambientais as $dado): ?>
            <tr>
                <td style="width:100px"><?php echo $dado['Cliente']['codigo'] ?></td>
                <td><?php echo $dado['Cliente']['razao_social'] ?></td>
                <td><?php echo $dado['Medico']['nome'] ?></td>
                <td class="input-medium"><?php echo $dado['Crra']['periodo'] ?></td>

                <td class="acoes">                
                <?php echo $this->Html->link('', array('action' => 'editar', $dado['Crra']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar Médico')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Crra']['count']; ?></td>
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
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    