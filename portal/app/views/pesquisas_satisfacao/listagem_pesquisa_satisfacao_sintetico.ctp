<?php if(empty($series)): ?>
    <div class="alert">
        Nenhum registro encontrado.
    </div>
<?php else: ?>
    <div id="grafico_pesquisa_satisfacao" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
    <?php echo $this->Javascript->codeBlock($this->Highcharts->render(array(), $series, array(
        'title' => '',
        'renderTo' => 'grafico_pesquisa_satisfacao',
        'chart' => array('type' => 'pie'),
        'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
        'plotOptions' => array('pie' => array('showInLegend'=>true)),
        'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false')))
    ))); ?>
<?php endif; ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-xlarge"><?php echo $agrupamentos[$agrupamento]; ?></th>
            <th class='input-mini numeric'>Total</th>
        </tr>
    </thead>
    <tbody>
        <?$total = 0;?>
        <?php foreach ($listagem as $key => $dados ):?>
            <?php $qtde_total    = (isset($dados[0]['codigo']) ? $dados[0]['codigo'] : -1)?>
            <?php $codigo_status = (isset($dados[0]['codigo']) ? $dados[0]['codigo'] : -1)?>
            <tr>
                <td class="input-xlarge">
                    <?= $this->Html->link( $dados[0]['nome'], "javascript:exibeAnalitico('{$agrupamento}',  '{$codigo_status}')") ?>
                </td>
                <td class="input-mini numeric">                    
                    <?= $this->Html->link( $this->Buonny->moeda( $dados[0]['total'], array('nozero'=>true, 'places'=>0)), "javascript:exibeAnalitico('{$agrupamento}', '{$qtde_total}')") ?>
                </td>
            </tr>
            <?$total += (!empty($dados[0]['total']) ? $dados[0]['total'] : 0)?>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Total</strong></td>            
            <td class='numeric'>
                <strong>
                <?= $this->Html->link( $this->Buonny->moeda( $total, array('nozero'=>true, 'places'=>0)), "javascript:exibeAnalitico('{$agrupamento}', '')") ?>
                </strong>
            </td>
        </tr>
    </tfoot>
</table>
<?php 
    $filtros['codigo_cliente'] = !empty($filtros['codigo_cliente']) ? $filtros['codigo_cliente'] : NULL;
    $filtros['codigo_status_pesquisa'] = !empty($filtros['codigo_status_pesquisa']) ? $filtros['codigo_status_pesquisa'] : NULL;
    $filtros['codigo_produto'] = !empty($filtros['codigo_produto']) ? $filtros['codigo_produto'] : NULL;
    $filtros['codigo_status_pesquisa'] = !empty($filtros['codigo_status_pesquisa']) ? $filtros['codigo_status_pesquisa'] : NULL;
    $filtros['status_pesquisa'] = !empty($filtros['status_pesquisa']) ? $filtros['status_pesquisa'] : NULL;
?>
<?php echo $this->Javascript->codeBlock("
    function exibeAnalitico( agrupamento, codigo_item ){
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('target', form_id);
        form.setAttribute('action', '/portal/pesquisas_satisfacao/pesquisa_satisfacao_analitico/popup/' + Math.random());

        field = document.createElement('input');
        field.setAttribute('name', 'data[PesquisaSatisfacao][codigo_cliente]');
        field.setAttribute('value', '{$filtros['codigo_cliente']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[PesquisaSatisfacao][codigo_status_pesquisa]');
        field.setAttribute('value', '{$filtros['codigo_status_pesquisa']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[PesquisaSatisfacao][codigo_produto]');
        field.setAttribute('value', '{$filtros['codigo_produto']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[PesquisaSatisfacao][codigo_status_pesquisa]');
        field.setAttribute('value', '{$filtros['codigo_status_pesquisa']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        if( agrupamento == 1){
            field.setAttribute('name', 'data[PesquisaSatisfacao][codigo_produto]');
        }else if( agrupamento == 2){
            field.setAttribute('name', 'data[PesquisaSatisfacao][codigo_usuario_pesquisa]');
        }else if( agrupamento == 3){
            field.setAttribute('name', 'data[PesquisaSatisfacao][codigo_status_pesquisa]');
        }
        field.setAttribute('value', codigo_item );
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        if( codigo_item < 0 ){
            field = document.createElement('input');
            field.setAttribute('name', 'data[PesquisaSatisfacao][status_pesquisa]');
            field.setAttribute('value', '1');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);
        } else {
            field = document.createElement('input');
            field.setAttribute('name', 'data[PesquisaSatisfacao][status_pesquisa]');
            field.setAttribute('value', '{$filtros['status_pesquisa']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);
        }
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
        document.body.appendChild(form);
        form.submit();
    }
", false );?>