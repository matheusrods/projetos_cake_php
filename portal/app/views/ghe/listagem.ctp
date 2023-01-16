<?php //debug($ghes)?>

<?php if (!empty($ghes)):?>
<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-medium">Código</th>
            <!--
                Falta definição dos campos / PC - 2532
                <th class="input-xlarge">Código externo GHE</th> 
            -->
            <th class="input-xlarge">Chave GHE</th>
            <th class="input-xlarge">APRHO parecer técnico </th>
            <th class="acoes" style="width:75px">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ghes as $dados): ?>
        <tr>
            <td class="input-mini">
                <?php echo $dados['Ghe']['codigo'] ?>
            </td>
            <!--
                Falta definição dos campos / PC - 2532
                <td class="input-xlarge">
                    ----
                </td> 
            -->
            <td class="input-xlarge">
                <?php echo $dados['Ghe']['chave_ghe'] ?>
            </td>
            <td class="input-xlarge">
                <?php echo $dados['Ghe']['aprho_parecer_tecnico']; ?>
            </td>            
            <td>
                <?php echo $html->link('', array('controller' => 'ghe', 'action' => 'trocar_status', $dados['Ghe']['codigo']), array('class' => 'icon-random', 'title' => 'Trocar Status do GHE')) ?>
                <?php if($dados['Ghe']['ativo']): ?>
                    <span class="badge badge-empty badge-success" title="Ativo"></span>
                <?php else: ?>
                    <span class="badge badge-empty badge-important" title="Inativo"></span>
                <?php endif; ?>
                <?php echo $this->Html->link('', array('action' => 'editar', $dados['Ghe']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Ghe']['count']; ?>
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
    function atualizaListaGhe() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'ghe/listagem/' + Math.random());
    }
");
