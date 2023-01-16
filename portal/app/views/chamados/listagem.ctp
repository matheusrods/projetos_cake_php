<?php //debug($chamados)?>

<?php if (!empty($chamados)):?>
<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-medium">Código</th>
            <th class="input-xxlarge">Título</th>
            <th class="input-xlarge">Tipo</th>
            <th class="input-xlarge">Responsável</th>
            <th class="input-xlarge">Status</th>
            <th class="acoes" style="width:75px">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($chamados as $dados): ?>
        <tr>
            <td class="input-mini">
                <?php echo $dados['Chamado']['codigo'] ?>
            </td>
            <td class="input-xlarge">
                <?php echo $dados['Chamado']['descricao'] ?>
            </td>
            <td class="input-xlarge">
                <?php echo $dados['ChamadoTipo']['descricao']; ?>
            </td>
            <td class="input-xlarge">
                <?php echo $dados['Responsavel']['nome'] ?>
            </td>
            <td class="input-mini">
                <?php echo $dados['ChamadoStatus']['descricao'] ?>
            </td>
            <td>
                <?php echo $this->Html->link('', array('action' => 'editar', $dados['Chamado']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar'));?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Chamado']['count']; ?>
            </td>
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

<?php else:?>
<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php
echo $this->Js->writeBuffer();

echo $this->Javascript->codeBlock("
    function atualizaListaChamados() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'chamados/listagem/' + Math.random());
    }
");
