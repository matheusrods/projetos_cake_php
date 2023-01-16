<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo $this->Paginator->sort('Pergunta', 'label') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($questoes as $dado): ?>
            <tr class="cliente-tr" codigo="<?php echo $dado['LabelQuestao']['codigo'] ?>" >
                <td class="input-mini"><?php echo $dado['LabelQuestao']['label'] ?></td>
            </tr>
        <?php endforeach ?>  
    </tbody>
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


    <?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
        $('tbody').attr('class', 'line-selector');
        $('tr.cliente-tr').click(function() {
            $('#QuestaoCodigoLabelQuestao').remove();
            $('#QuestaoLabel').val('');
            $('#QuestaoLabel')
            .val($(this).children('td').text())
            .parents('form')
            .prepend( $('<input>', {name: 'data[Questao][codigo_label_questao]', id: 'QuestaoCodigoLabelQuestao', type: 'hidden', value: $(this).attr('codigo') }) );
            close_dialog();
        })
    })"); ?>

<?php echo $this->Js->writeBuffer(); ?>

