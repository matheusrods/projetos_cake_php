<?php if(!empty($atendimentos)):?>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    <div id="grafico" style="width:100%;height:450px;float:left"></div>
    <br/>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="input-xxlarge">Descrição</th>
                <th class="input-mini numeric">Quantidade</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0;?>
            <?php foreach ($atendimentos as $atendimento): ?>
            <tr>
                <?php $total += $atendimento[0]['qtd']; ?>
                <?php $codigo_selecionado = !empty($atendimento[0]['codigo']) ? $atendimento[0]['codigo'] : '-1';?>
                <td><?php echo $atendimento[0]['descricao']; ?></td>
                <td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($atendimento[0]['qtd'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}')") ?>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "1"><strong>Total</strong></td>
                <td class="numeric" colspan = "1"><?= $total;?></td>
            </tr>
        </tfoot>    
    </table>
    <?php echo $this->Javascript->codeBlock("
        function analitico(codigo_selecionado) {
            var agrupamento = {$agrupamento}; 
        
            var form = document.createElement('form');
            var form_id = ('formresult' + Math.random()).replace('.','');
            form.setAttribute('method', 'post');
            form.setAttribute('target', form_id);
            form.setAttribute('action', '/portal/atendimentos_sacs/analitico/1/' + Math.random());
           
            if(agrupamento == 1){
                field = document.createElement('input');
                field.setAttribute('name', 'data[AtendimentoSac][nome_atendente]');     
                field.setAttribute('value', codigo_selecionado);
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }

            if(agrupamento == 2){
                field = document.createElement('input');
                field.setAttribute('name', 'data[AtendimentoSac][codigo_transportador]');     
                field.setAttribute('value', codigo_selecionado);
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
            if(agrupamento == 3){
                field = document.createElement('input');
                field.setAttribute('name', 'data[AtendimentoSac][codigo_embarcador]');     
                field.setAttribute('value', codigo_selecionado);
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
            if(agrupamento == 4){
                field = document.createElement('input');
                field.setAttribute('name', 'data[AtendimentoSac][codigo_motivo_atendimento]');     
                field.setAttribute('value', codigo_selecionado);
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
            if(agrupamento == 5){
                field = document.createElement('input');
                field.setAttribute('name', 'data[AtendimentoSac][tecnologia]');     
                field.setAttribute('value', codigo_selecionado);
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
                   
            var janela = window_sizes();
            window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
            document.body.appendChild(form);
            form.submit();

        }"
    );?>
<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGrafico['eixo_x'], $dadosGrafico['series'], array(
    'renderTo' => 'grafico',
    'chart' => array('type' => 'column'),
    'yAxis' => array('title' => ''),
    'xAxis' => array('labels' => array('rotation' => -10, 'y' => 20), 'gridLineWidth' => 1),
    'tooltip' => array('formatter' => 'this.y'),
    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'),'printButton' => array('enabled'=> 'false'))),
))); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>
