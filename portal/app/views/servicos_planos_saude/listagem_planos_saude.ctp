<?php if(!empty($planos)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
             <th class="input-small">Código</th>
             <th class="input-xxlarge">Descrição</th>        
             <th class="input-mini" style="text-align:center">Ações</th>
         </tr>
     </thead>
     <tbody>
        <?php foreach ($planos as $servico): ?>
            <tr>
                <td class="input-small"><?php echo $servico['Servico']['codigo'] ?></td>
                <td class="input-xxlarge"><?php echo $servico['Servico']['descricao'] ?></td>
                <td style="text-align:center">
                    <?php echo $this->Html->link('', array('action' => 'selecionar_servicos', $servico['Servico']['codigo']), array('class' => 'icon-wrench', 'title' => 'Editar')); ?>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Servico']['count']; ?></td>
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
        <div class="alert">Não foi encontrado nenhum serviço classificado como plano de saúde.</div>
    <?php endif;?>    