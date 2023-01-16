<?php
    switch ($this->data['Sinistro']['agrupamento']) {
        case Sinistro::AGRP_TRANSPORTADOR:
            $label = 'Transportadoras';
            $optionLabel = 'this.point.option';
            break;
        case Sinistro::AGRP_SEGURADOR:
            $label = 'Seguradoras';
            $optionLabel = 'this.point.name';
            break;
        case Sinistro::AGRP_CORRETOR:
            $label = 'Corretoras';
            $optionLabel = 'this.point.name';
            break;
        case Sinistro::AGRP_MOTORISTA:
            $label = 'Motoristas';
            $optionLabel = 'this.point.name';
            break;
        case Sinistro::AGRP_EMBARCADOR:
            $label = 'Embarcadores';
            $optionLabel = 'this.point.option';
            break;
        case Sinistro::AGRP_TECNOLOGIA:
            $label = 'Tecnologia';
            $optionLabel = 'this.point.name';
            break;
        case Sinistro::AGRP_SINISTRO:
            $label = 'Sinistro';
            $optionLabel = 'this.point.name';
            break;
    }
?>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php if(!empty($dados)): ?>

    <div class="row-fluid">
        <div class="span6" style="min-height: 200px">
            <h4>Dia da Semana</h4>
            <div id="relatorio-dia-semana"></div>
        </div>
        <div class="span6" style="min-height: 200px">
            <h4>Estado</h4>
            <div id="relatorio-estado"></div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6" style="min-height: 200px">
            <h4>Mêses</h4>
            <div id="relatorio-mensal"></div>
        </div>
        <div class="span6" style="min-height: 200px">
            <h4><?php echo $label; ?></h4>
            <div id="relatorio-agrupamento"></div>
        </div>
    </div>

<table class='table table-striped'>
    <thead>
        <th class='input-small'>Código</th>
        <th><?php echo  $label ?></th>
        <th class='numeric input-small'>Quantidade</th>
    </thead>
    <tbody>
        <?php if( isset($dados) && !empty($dados) ): ?>
            <?php foreach($dados as $key=>$value): ?>
                <tr>
                    <td class='input-small'><?php echo utf8_encode($value[0]['codigo']) ?></td>
                	<td><?php echo utf8_encode($value[0]['descricao']) ?></td>
                    <td class='numeric input-small'><?= $this->Html->link($value[0]['qtd_ocorrencias'], 'javascript:void(0)', array('onclick' => "analitico('{$value[0]['codigo']}')")) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php echo $this->Javascript->codeBlock("
    function analitico(codigo_selecionado) {
        var agrupamento = {$this->data['Sinistro']['agrupamento']};
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('target', form_id);
        form.setAttribute('action', '/portal/sinistros/sinistros_analitico/' + Math.random());
       
        field = document.createElement('input');
        field.setAttribute('name', 'data[Sinistro][data_inicial]');
        field.setAttribute('value', '{$this->data['Sinistro']['data_inicial']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[Sinistro][data_final]');
        field.setAttribute('value', '{$this->data['Sinistro']['data_final']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
       
        field = document.createElement('input');
        field.setAttribute('name', 'data[Sinistro][codigo]');
        field.setAttribute('value', '{$this->data['Sinistro']['codigo']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[Sinistro][sm]');
        field.setAttribute('value', '{$this->data['Sinistro']['sm']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[Sinistro][natureza]');
        field.setAttribute('value', '{$this->data['Sinistro']['natureza']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[Sinistro][codigo_embarcador]');
        field.setAttribute('value', (agrupamento == 1 ? codigo_selecionado : '{$this->data['Sinistro']['codigo_embarcador']}'));
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[Sinistro][codigo_transportador]');
        field.setAttribute('value', (agrupamento == 2 ? codigo_selecionado : '{$this->data['Sinistro']['codigo_transportador']}'));
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[Sinistro][codigo_seguradora]');
        field.setAttribute('value', (agrupamento == 3 ? codigo_selecionado : '{$this->data['Sinistro']['codigo_seguradora']}'));
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[Sinistro][codigo_corretora]');
        field.setAttribute('value', (agrupamento == 4 ? codigo_selecionado : '{$this->data['Sinistro']['codigo_corretora']}'));
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[Sinistro][codigo_documento_profissional]');
        field.setAttribute('value', (agrupamento == 5 ? codigo_selecionado : '{$this->data['Sinistro']['codigo_documento_profissional']}'));
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[Sinistro][natureza]');
        field.setAttribute('value', (agrupamento == 6 ? codigo_selecionado : '{$this->data['Sinistro']['codigo_corretora']}'));
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[Tecnologia][codigo]');
        field.setAttribute('value', (agrupamento == 7 ? codigo_selecionado : '{$this->data['Sinistro']['codigo_corretora']}'));
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
        form.submit();
    }") 
?>
<?php echo $this->Javascript->codeBlock($this->Highcharts->render(array(), $dadosGrafico['series'], array(
    'title' => '',
    'renderTo' => 'relatorio-agrupamento',
    'chart' => array('type' => 'pie'),
    'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
    'plotOptions' => array('pie' => array('showInLegend'=>true)),
    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false'))),
    'tooltip' => array('formatter' => "'<b>'+ ".$optionLabel." +'</b><br/>'+this.y"),
))); ?>
<?php echo $this->Javascript->codeBlock($this->Highcharts->render(array(), $dadosGraficoEstado['series'], array(
    'title' => '',
    'renderTo' => 'relatorio-estado',
    'chart' => array('type' => 'pie'),
    'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
    'plotOptions' => array('pie' => array('showInLegend'=>true)),
    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false')))
))); ?>
<?php echo $this->Javascript->codeBlock($this->Highcharts->render(array(), $dadosGraficoSemanal['series'], array(
    'title' => '',
    'renderTo' => 'relatorio-dia-semana',
    'chart' => array('type' => 'pie'),
    'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
    'plotOptions' => array('pie' => array('showInLegend'=>true)),
    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false')))
))); ?>
<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGraficoMensal['eixo_x'], $dadosGraficoMensal['series'], array(
    'renderTo' => 'relatorio-mensal',
    'chart' => array('type' => 'column', 'spacingBottom' => 70),
    'yAxis' => array('title' => false),
    'xAxis' => array('labels' => array('rotation' => -75, 'y' => 30), 'gridLineWidth' => 1),
    'legend' => array('enabled' => 'false'),
    'exporting' => array('buttons' => array('exportButton' => array('enabled' => 'false'), 'printButton' => array('enabled' => 'false')),),
)));
?>

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>