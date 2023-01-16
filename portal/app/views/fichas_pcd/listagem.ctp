<?php if(!empty($fichas_pcd)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium"><?= $this->Paginator->sort('Código Ficha','FichaClinica.codigo')?></th>
               <th class="input-medium"><?= $this->Paginator->sort('Código Pedido','FichaClinica.codigo_pedido_exame')?></th>
               <th class="input-medium"><?= $this->Paginator->sort('Cliente','Cliente.razao_social')?></th>
               <th class="input-medium"><?= $this->Paginator->sort('Funcionário','Funcionario.nome')?></th>
               <th class="input-medium"><?= $this->Paginator->sort('Médico','Medico.nome')?></th>
            	<th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fichas_pcd as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['FichaClinica']['codigo'] ?></td>
                <td class="input-mini"><?php echo $dados['FichaClinica']['codigo_pedido_exame'] ?></td>
                <td class="input-mini"><?php echo $dados['Cliente']['razao_social'] ?></td>
                <td class="input-mini"><?php echo $dados['Funcionario']['nome'] ?></td>
                <td class="input-mini"><?php echo $dados['Medico']['nome'] ?></td>
                <td>
                    <?php echo $this->Html->link('', array('action' => 'imprimir_relatorio', $dados['FichaClinica']['codigo']), array('data-toggle' => 'tooltip', 'title' => 'Imprimir relatório', 'class' => 'icon-print ')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['FichaClinica']['count']; ?></td>
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