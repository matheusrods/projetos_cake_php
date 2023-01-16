<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?= $this->Paginator->sort('Código', 'cdfv_codigo') ?></th>
            <th><?= $this->Paginator->sort('Faixa de Valores', 'cdfv_descricao') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Mínimo', 'cdfv_valor_minimo') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Maximo', 'cdfv_valor_maximo') ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listagem as $item): ?>
        <tr>
            <td class="input-mini">
                <?= $item['TCdfvCriterioFaixaValor']['cdfv_codigo'] ?>
            </td>
            <td>
                <?= $item['TCdfvCriterioFaixaValor']['cdfv_descricao'] ?>
            </td>
            <td class="numeric">
                <?= $this->Buonny->moeda($item['TCdfvCriterioFaixaValor']['cdfv_valor_minimo']) ?>
            </td>
            <td class="numeric">
                <?= $this->Buonny->moeda($item['TCdfvCriterioFaixaValor']['cdfv_valor_maximo']) ?>
            </td>
            <td class="input-mini">
                <?= $html->link('', array('action' => 'editar', $item['TCdfvCriterioFaixaValor']['cdfv_codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
				<?php echo $html->link('', array('controller' => 'faixas_valores', 'action' => 'excluir', $item['TCdfvCriterioFaixaValor']['cdfv_codigo']), array('class' => 'icon-trash', 'title' => 'Excluir área de atuação'), 'Confirma exclusão?'); ?>
            </td>
        </tr>
        <?php endforeach; ?>        
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

<?php echo $this->Js->writeBuffer(); ?>