<?php if(!empty($registros_telecom)):?>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    <div id="grafico" style="width:100%;height:450px;float:left"></div>
    <br/>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="input-xxlarge">Descrição</th>
                <th class="input-mini numeric">Quantidade</th>
                <th class="input-mini numeric">Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0;?>
            <?php $total_valor = 0;?>
            <?php foreach ($registros_telecom as $registros_telecom): ?>
            <tr>
                <?php $total += $registros_telecom[0]['qtd']; ?>
                <?php $total_valor += $registros_telecom[0]['valor']; ?>
                <?php $codigo_selecionado = !empty($registros_telecom[0]['codigo']) ? $registros_telecom[0]['codigo'] : '-1';?>
                <td><?php echo (utf8_encode($registros_telecom[0]['descricao']) ? utf8_encode($registros_telecom[0]['descricao']) : 'Sem departamento'); ?></td>
                <td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($registros_telecom[0]['qtd'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}')") ?>
                <td class='numeric input-small'><?= $this->Buonny->moeda($registros_telecom[0]['valor']) ?>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "1"><strong>Total</strong></td>
                <td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($total, array('nozero' => true, 'places' => 0)), "javascript:analitico('')") ?>
                <td class='numeric input-small' colspan = "1"><?= $this->Buonny->moeda($total_valor) ?>
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
            form.setAttribute('action', '/portal/registros_telecom/analitico/1/' + Math.random());
           
            if(agrupamento == 1){
                field = document.createElement('input');
                field.setAttribute('name', 'data[RegistroTelecom][apelido]');     
                field.setAttribute('value', codigo_selecionado);
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
            if(agrupamento == 2){
                field = document.createElement('input');
                field.setAttribute('name', 'data[RegistroTelecom][codigo_departamento]');     
                field.setAttribute('value', codigo_selecionado);
                field.setAttribute('type', 'hidden');
                form.appendChild(field);
            }
            if(agrupamento == 3){
                field = document.createElement('input');
                field.setAttribute('name', 'data[RegistroTelecom][codigo_operadora]');     
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

<?php //debug($dadosGrafico) ?>


<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGrafico['eixo_x'], $dadosGrafico['series'], array(
    'renderTo' => 'grafico',
    'chart' => array('type' => 'column'),
    'yAxis' => array('title' => ''),
    'xAxis' => array('labels' => array('rotation' => ($quantia_registros <= 35 ? -10 : -90), 'y' => 20), 'gridLineWidth' => 1),
    'tooltip' => array('formatter' => 'this.y'),
    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'),'printButton' => array('enabled'=> 'false'))),
))); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>
