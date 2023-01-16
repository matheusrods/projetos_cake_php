<?php if(!empty($dados_glosas)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

    <table class="table table-striped">
        <thead>
            <tr>
            <th class="input-mini"><?= $this->Paginator->sort('Código da Glosa', 'codigo')?></th>
            <th><?= $this->Paginator->sort('Código Pedido Exame','codigo_pedidos_exames')?></th>
            <th><?= $this->Paginator->sort('Exame','descricao')?></th>
            <th><?= $this->Paginator->sort('Valor', 'valor')?></th>
            <th><?= $this->Paginator->sort('Data da Glosa','data_glosa')?></th>
            <th><?= $this->Paginator->sort('Data de Vencimento','data_vencimento')?></th>
            <th><?= $this->Paginator->sort('Data de Pagamento','data_pagamento')?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Status','codigo_nota_fiscal_status')?></th>
            <th><?= $this->Paginator->sort('Motivo da Glosa','motivo_glosa')?></th>
            <th></th>
            <th class="acoes" style="width:89px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dados_glosas as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['Glosa']['codigo'] ?></td>
                <td><?php echo $dados['Glosa']['codigo_pedidos_exames'] ?></td>
                <td><?php echo $dados['Exame']['descricao'] ?></td>
                <td class="input-mini"><?php echo $this->Buonny->moeda($dados['Glosa']['valor']) ?></td>
                <td><?php echo $dados['Glosa']['data_glosa'] ?></td>
                <td><?php echo $dados['Glosa']['data_vencimento'] ?></td>
                <td><?php echo $dados['Glosa']['data_pagamento'] ?></td>
                <td><?php switch ($dados['Glosa']['codigo_status_glosa']){
                    case '1':
                        echo 'A pagar';
                        break;
                    case '2':
                        echo 'Paga';
                        break;
                    case '3':
                        echo 'Indevida';
                        break;
                    } ?>
                </td>
                <td></td>
                <td style="width:50px;">              
                    <?php echo $this->Html->link('', array('controller' => 'notas_fiscais_servico', 'action' => 'editar', $dados['Glosas']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); 
                    ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "15"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Glosas']['count']; ?></td>
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

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>  
  