<div class='well'>
    <?php echo $this->BForm->create('TEviaEstaViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'estatisticas_viagens', 'action' => 'por_tecnologia'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('tecn_codigo', array('class' => 'input-medium', 'label' => false, 'options' => $tecnologias,'empty' => 'Tecnologia')) ?>
            <?php echo $this->BForm->input('tipo', array('class' => 'input-medium', 'label' => false, 'options' => $tipos)) ?>
            <?php echo $this->BForm->input('data', array('class' => 'data input-small', 'placeholder' => 'Data', 'label' => false, 'type' => 'text')) ?>
        </div>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php $this->addScript($this->Buonny->link_js('estatisticas2')) ?>
<?php if (!empty($lista)): ?>
    <table class="table table-striped table-bordered tablesorter">
        <thead>
            <tr>
                <th class="" title="Tecnologia"><?= $this->Html->link('Tecnologia', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Embarcadores"><?= $this->Html->link('Embarcadores', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Transportadores"><?= $this->Html->link('Transportadores', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Operadores"><?= $this->Html->link('Operadores', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Seguradoras"><?= $this->Html->link('Seguradoras', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Corretoras"><?= $this->Html->link('Corretoras', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de SMs abertas"><?= $this->Html->link('SMs Abertas', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de SMs Monitoradas"><?= $this->Html->link('SMs Monitoradas', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Média de SMs Monitoradas por Operador"><?= $this->Html->link('Média', 'javascript:void(0)') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista as $operacao): ?>
                <tr>
                    <td class=""><?= $operacao[$model][$prefixo.'_tecn_descricao'] ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$prefixo.'_emba_total'], 'javascript:void(0)', array('onclick' => 'estatistica_por_embarcador(this)')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$prefixo.'_tran_total'], 'javascript:void(0)', array('onclick' => 'estatistica_por_transportador(this)')) ?></td>
                    <td class="numeric"><?= $operacao[$model][$prefixo.'_oper_total'] ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$prefixo.'_segu_total'], 'javascript:void(0)', array('onclick' => 'estatistica_por_seguradora(this)')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$prefixo.'_corr_total'], 'javascript:void(0)', array('onclick' => 'estatistica_por_corretora(this)')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$prefixo.'_sm_em_aberto'], 'javascript:void(0)', array('onclick' => 'estatistica_por_cliente(this)')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[$model][$prefixo.'_sm_em_andamento'], 'javascript:void(0)', array('onclick' => 'estatistica_por_cliente(this)')) ?></td>
                    <td class="numeric"><?= $this->Buonny->moeda(round($operacao[$model][$prefixo.'_em_andamento_por_operador'],2), array('edit' => true)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Javascript->codeBlock("
    $.tablesorter.addParser({
        id: 'datetime',
        is: function(s) {
            return false; 
        },
        format: function(s,table) {
            s = s.replace(/\-/g,'/');
            s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, '$3/$2/$1');
            return $.tablesorter.formatFloat(new Date(s).getTime());
        },
        type: 'numeric'
    });
    jQuery('table.table').tablesorter({
        sortList: [[0,1]], 
        dateFormat: 'dd/mm/yyyy',
        headers: {
            0: {sorter: 'datetime'}
        }
    })")) ?>
<?php endif; ?>