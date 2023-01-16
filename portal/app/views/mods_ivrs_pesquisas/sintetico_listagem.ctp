</br>
<?php if(!empty($registros_ura)):?>
    <?php
        echo $this->Paginator->options(array('update' => 'div.lista'));
    $total_qta_pt_null = null;
    $total_qta_pt0 = null;
    $total_qta_pt1 = null;
    $total_qta_pt2 = null;
    $total_qta_pt3 = null;
    $total_qta_pt4 = null;
    $total_qta_pt5 = null;
    $total_geral = null;
    $sem_valor = $agrupamento == 1 ? 'Sem Departamento' : 'Sem Ramal';

    ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-large"><?php echo $this->Paginator->sort('Descricao', 'descricao') ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Sem nota', 'qta_pt_null') ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Nota 0', 'qta_pt0') ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Nota 1', 'qta_pt1') ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Nota 2', 'qta_pt2') ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Nota 3', 'qta_pt3') ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Nota 4', 'qta_pt4') ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Nota 5', 'qta_pt5') ?></th>
            <th class="input-mini"><?php echo $this->Paginator->sort('Total', 'total') ?></th>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($registros_ura as $registro_ura): ?>
        <tr>
            <?php $total_qta_pt_null += (!empty($registro_ura[0]['qta_pt_null']) ? $registro_ura[0]['qta_pt_null'] : 0) ?>
            <?php $total_qta_pt0 += (!empty($registro_ura[0]['qta_pt0']) ? $registro_ura[0]['qta_pt0'] : 0) ?>
            <?php $total_qta_pt1 += (!empty($registro_ura[0]['qta_pt1']) ? $registro_ura[0]['qta_pt1'] : 0) ?>
            <?php $total_qta_pt2 += (!empty($registro_ura[0]['qta_pt2']) ? $registro_ura[0]['qta_pt2'] : 0) ?>
            <?php $total_qta_pt3 += (!empty($registro_ura[0]['qta_pt3']) ? $registro_ura[0]['qta_pt3'] : 0) ?>
            <?php $total_qta_pt4 += (!empty($registro_ura[0]['qta_pt4']) ? $registro_ura[0]['qta_pt4'] : 0) ?>
            <?php $total_qta_pt5 += (!empty($registro_ura[0]['qta_pt5']) ? $registro_ura[0]['qta_pt5'] : 0) ?>
            <?php $total_geral += (!empty($registro_ura[0]['total']) ? $registro_ura[0]['total'] : 0) ?>
            
            <?php $codigo_selecionado = (!empty($registro_ura[0]['codigo']) ? $registro_ura[0]['codigo'] : '99') ?>
            <td ><?php echo ($registro_ura[0]['descricao'] ? $registro_ura[0]['descricao'] : $sem_valor); ?></td>
            <td ><?php echo $this->Html->link((!empty($registro_ura[0]['qta_pt_null']) ? $registro_ura[0]['qta_pt_null']." (".number_format(($registro_ura[0]['qta_pt_null']/$registro_ura[0]['total'])*100, 2)."%)" : null), 'javascript:void(0)', array('onclick' => "analitico('{$codigo_selecionado}', '99')")) ?></td>
            <td ><?php echo $this->Html->link((!empty($registro_ura[0]['qta_pt0']) ? $registro_ura[0]['qta_pt0']." (".number_format(($registro_ura[0]['qta_pt0']/$registro_ura[0]['total'])*100, 2)."%)" : null), 'javascript:void(0)', array('onclick' => "analitico('{$codigo_selecionado}', '0')")) ?></td>
            <td ><?php echo $this->Html->link((!empty($registro_ura[0]['qta_pt1']) ? $registro_ura[0]['qta_pt1']." (".number_format(($registro_ura[0]['qta_pt1']/$registro_ura[0]['total'])*100, 2)."%)" : null), 'javascript:void(0)', array('onclick' => "analitico('{$codigo_selecionado}', '1')")) ?></td>
            <td ><?php echo $this->Html->link((!empty($registro_ura[0]['qta_pt2']) ? $registro_ura[0]['qta_pt2']." (".number_format(($registro_ura[0]['qta_pt2']/$registro_ura[0]['total'])*100, 2)."%)" : null), 'javascript:void(0)', array('onclick' => "analitico('{$codigo_selecionado}', '2')")) ?></td>
            <td ><?php echo $this->Html->link((!empty($registro_ura[0]['qta_pt3']) ? $registro_ura[0]['qta_pt3']." (".number_format(($registro_ura[0]['qta_pt3']/$registro_ura[0]['total'])*100, 2)."%)" : null), 'javascript:void(0)', array('onclick' => "analitico('{$codigo_selecionado}', '3')")) ?></td>
            <td ><?php echo $this->Html->link((!empty($registro_ura[0]['qta_pt4']) ? $registro_ura[0]['qta_pt4']." (".number_format(($registro_ura[0]['qta_pt4']/$registro_ura[0]['total'])*100, 2)."%)" : null), 'javascript:void(0)', array('onclick' => "analitico('{$codigo_selecionado}', '4')")) ?></td>
            <td ><?php echo $this->Html->link((!empty($registro_ura[0]['qta_pt5']) ? $registro_ura[0]['qta_pt5']." (".number_format(($registro_ura[0]['qta_pt5']/$registro_ura[0]['total'])*100, 2)."%)" : null), 'javascript:void(0)', array('onclick' => "analitico('{$codigo_selecionado}', '5')")) ?></td>
            <td ><?php echo $this->Html->link((!empty($registro_ura[0]['total']) ? $registro_ura[0]['total'] : null), 'javascript:void(0)', array('onclick' => "analitico('{$codigo_selecionado}')")) ?></td>
        </tr>
    <?php endforeach ?>
</tbody>
    <tfoot>
        <tr>
            <td><strong><?php echo 'Total' ?></strong></td>
            <td><?php echo $this->Html->link(($total_qta_pt_null), 'javascript:void(0)', array('onclick' => "analitico('', '0')")) ?></td>
            <td><?php echo $this->Html->link(($total_qta_pt0), 'javascript:void(0)', array('onclick' => "analitico('', '0')")) ?></td>
            <td><?php echo $this->Html->link(($total_qta_pt1), 'javascript:void(0)', array('onclick' => "analitico('', '1')"))?></td>
            <td><?php echo $this->Html->link(($total_qta_pt2), 'javascript:void(0)', array('onclick' => "analitico('', '2')"))?></td>
            <td><?php echo $this->Html->link(($total_qta_pt3), 'javascript:void(0)', array('onclick' => "analitico('', '3')"))?></td>
            <td><?php echo $this->Html->link(($total_qta_pt4), 'javascript:void(0)', array('onclick' => "analitico('', '4')"))?></td>
            <td><?php echo $this->Html->link(($total_qta_pt5), 'javascript:void(0)', array('onclick' => "analitico('', '5')"))?></td>
            <td><?php echo $this->Html->link(($total_geral),   'javascript:void(0)', array('onclick' => "analitico('', '')")) ?></td>
        </tr>
    </tfoot>    
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
        <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disable paginacao_anterior')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disable paginacao_proximo')); ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock("
        function analitico(codigo_selecionado, pontuacao) {
            var form = document.createElement('form');
            var form_id = ('formresult' + Math.random()).replace('.','');
            form.setAttribute('method', 'post');
            form.setAttribute('target', form_id);
            form.setAttribute('action', '/portal/mods_ivrs_pesquisas/analitico/1/' + Math.random())
            
            if('{$agrupamento}'== 1) {
                field = document.createElement('input');
                field.setAttribute('name', 'data[ModIvrPesquisa][departamento]');
                field.setAttribute('value', codigo_selecionado);
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
            if('{$agrupamento}'== 2) {
                field = document.createElement('input');
                field.setAttribute('name', 'data[ModIvrPesquisa][agtext]');
                field.setAttribute('value', codigo_selecionado);
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
            if(pontuacao) {
                field = document.createElement('input');
                field.setAttribute('name', 'data[ModIvrPesquisa][score]');     
                field.setAttribute('value', pontuacao);
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            } else {
                field = document.createElement('input');
                field.setAttribute('name', 'data[ModIvrPesquisa][score]');     
                field.setAttribute('value', '');
                field.setAttribute('type', 'hidden');
                form.appendChild(field);   
            }

            field = document.createElement('input');
            field.setAttribute('name', 'data[ModIvrPesquisa][startq]');
            field.setAttribute('value', '{$this->data['ModIvrPesquisa']['startq']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[ModIvrPesquisa][endq]');
            field.setAttribute('value', '{$this->data['ModIvrPesquisa']['endq']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

          
            var janela = window_sizes();
            window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
            document.body.appendChild(form);
            form.submit();

        }"
    );?>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    