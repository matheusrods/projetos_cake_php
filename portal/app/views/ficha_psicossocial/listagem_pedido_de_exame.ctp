<?php if(!empty($pedidosExames)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código Pedido de Exame</th>
               <th class="input-medium">Cliente</th>
               <th class="input-medium">Funcionário</th>
            	<th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidosExames as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['PedidoExame']['codigo'] ?></td>
                <td class="input-mini"><?php echo $dados['Cliente']['razao_social'] ?></td>
                <td class="input-mini"><?php echo $dados['Funcionario']['nome'] ?></td>
                <td>
                <?php echo $this->Html->link('Criar Ficha', array('action' => 'incluir', $dados['PedidoExame']['codigo']), array('class' => 'btn btn-default btn-small', 'title' => 'Editar')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "4"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['PedidoExame']['count']; ?></td>
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
    <div class='form-actions well'>
        <?php echo $html->link('Voltar', array('controller' => 'ficha_psicossocial', 'action' => 'listagem_ficha_psicossocial', $codigo_funcionario_setor_cargo, 0, $codigo_cliente_funcionario), array('class' => 'btn btn-default')); ?>
    </div>
   
    <?php else:?>
        <div class="alert">Nenhum dado foi encontrado.</div>
          <div class='form-actions well'>
            <?php echo $html->link('Voltar', array('controller' => 'ficha_psicossocial', 'action' => 'listagem_ficha_psicossocial', $codigo_funcionario_setor_cargo, 0, $codigo_cliente_funcionario), array('class' => 'btn btn-default')); ?>
        </div>
<?php endif;?>    