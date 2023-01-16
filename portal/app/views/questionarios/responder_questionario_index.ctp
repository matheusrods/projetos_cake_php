<?php if(!empty($questionarios)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Descrição</th>
               <th class="input-medium">Observações</th>
               <th class="acoes" style="width:75px">Ações</th>
           </tr>
       </thead>
       <tbody>
        <?php foreach ($questionarios as $dados) { ?>
            <tr>
                <td class="input-mini"><?php echo $dados['Questionario']['descricao'] ?></td>
                <td class="input-mini"><?php echo $dados['Questionario']['observacoes'] ?></td>
                <td>
                    <?php 
                    if($dados[0]['LastAnswer'] < 1) { 
                    echo $this->Html->link('', array('action' => 'responder_questionario', $dados['Questionario']['codigo']), array('class' => 'icon-play', 'data-toggle' => 'tooltip', 'title' => 'Iniciar questionário')); 
                    } else {
                        echo '<i class="icon-play not-allowed" data-toggle="tooltip" title="Este formulário ja foi respondido."></i>&nbsp;&nbsp;';    
                        echo $this->Html->link('', array('action' => 'listagem_resultados', $dados['Questionario']['codigo']), array('class' => 'icon-tasks', 'data-toggle' => 'tooltip', 'title' => 'Vizualizar respostas'));
                    }
                    ?> 
                </td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Questionario']['count']; ?></td>
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