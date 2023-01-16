<?php if(!empty($questionarios)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
             <th class="input-medium"><?php echo $this->Paginator->sort('Código','Questionario.codigo')?></th>
             <th class="input-medium"><?php echo $this->Paginator->sort('Descrição','Questionario.descricao')?></th>
             <th class="input-medium"><?php echo $this->Paginator->sort('Observações','Questionario.observacoes')?></th>
             <th class="acoes" style="width:75px">Ações</th>
         </tr>
     </thead>
     <tbody>
        <?php foreach ($questionarios as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['Questionario']['codigo'] ?></td>
                <td class="input-mini"><?php echo $dados['Questionario']['descricao'] ?></td>
                <td class="input-mini"><?php echo $dados['Questionario']['observacoes'] ?></td>
                <td>
                    <?php echo $this->Html->link('', array('controller' => 'resultados', 'action' => 'alterar', $dados['Questionario']['codigo']), array('class' => 'icon-flag', 'data-toggle' => 'tooltip', 'title' => 'Definir resultado de pontuação')); ?> &nbsp;

                    <?php echo $this->Html->link('', array('action' => 'editar', $dados['Questionario']['codigo']), array('class' => 'icon-edit ', 'data-toggle' => 'tooltip', 'title' => 'Editar')); ?> &nbsp;

                    <?php echo $this->Html->link('', array('action' => 'excluir', $dados['Questionario']['codigo']), array('class' => 'icon-trash delete-confirm', 'data-toggle' => 'tooltip', 'title' => 'Excluir', 'data-title' => 'Tem certeza?', 'data-text' => 'Esta operação também exclui as questões vinculadas a este questionário')); ?> &nbsp;

                    <?php echo $this->Html->link('', array('controller' => 'questoes', 'action' => 'index', $dados['Questionario']['codigo']), array('class' => 'icon-wrench', 'data-toggle' => 'tooltip', 'title' => 'Formatar questões')); ?>

                    <?php 
                    //esta funcionalidade somente server por enquanto para os questionarios 13 e 16
                    if($dados['Questionario']['codigo_questionario_tipo'] == 1) {
                        echo $this->Html->link('', array('controller' => 'questionarios', 'action' => 'retira_permissao', $dados['Questionario']['codigo']), array('class' => 'icon-ban-circle', 'data-toggle' => 'tooltip', 'title' => 'Retirar Permissões')); 
                    }
                    else if($dados['Questionario']['codigo_questionario_tipo'] == 2) {
                        echo $this->Html->link('', array('controller' => 'questionarios', 'action' => 'permissao', $dados['Questionario']['codigo']), array('class' => 'icon-lock', 'data-toggle' => 'tooltip', 'title' => 'Permissões')); 
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach ?>
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
<?php echo $this->Js->writeBuffer(); ?>
<?php 
echo $this->Javascript->codeBlock("
 $(document).ready(function() {
    $('[data-toggle=\"tooltip\"]').tooltip();
   deleteAlert();
});
function atualizaQuestionarios() {
   var div = jQuery('div.lista');
   bloquearDiv(div);
   div.load(baseUrl + 'questionarios/listagem/' + Math.random());
}
");
?>
<?php // echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    