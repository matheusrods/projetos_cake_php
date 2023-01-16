<div class='well'>
    <?php echo $this->BForm->create('RelatorioEstatisticoSm', array('autocomplete' => 'off', 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'transportadora_tecnologia_por_sm'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', False,'RelatorioEstatisticoSm') ?>
            <?php echo $this->Buonny->input_periodo($this,'RelatorioEstatisticoSm','data_inicial','data_final') ?>
        </div>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php if( isset( $dados ) && !empty( $dados['series'] ) ): ?>
    <div id="show-chart" style="min-width: 400px; height: 400px; margin: 0 auto 50px">
        <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
        <?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
        <?php echo $this->Javascript->codeBlock($this->Highcharts->render($dados['eixo_x'], $dados['series'], array(
            'renderTo' => 'show-chart',
            'chart' => array('type' => 'pie'),
        ))); ?>
    </div>
<?php endif; ?>
<?php if(isset($cliente['Cliente'])): ?>
    <div class="well">
        <strong>Cliente:</strong> <?php echo $cliente['Cliente']['razao_social']; ?>
    </div>
<?php endif; ?>
<?php if (isset($data) && count($data)>0 && $data!==false): ?>
	<table class='table table-striped tablesorter'>
		<thead>
			<th><?php echo $this->Html->link('Codigo', 'javascript:void(0)') ?></th>            
			<th><?php echo $this->Html->link('Tecnologia', 'javascript:void(0)') ?></th>
			<th class='numeric'><?php echo $this->Html->link('Encerradas', 'javascript:void(0)') ?></th>
		</thead>
        <?php $totalRegistros = 0; ?>
        
            <?php foreach ($data as $dados): ?>
			<tr>               
                <td><?php echo $dados[0]['codigo'] ?></td>
				<td><?php echo $dados[0]['descricao'] ?></td>
                <td class='numeric'>
                    <?php
                        echo $this->Html->link( $this->Buonny->moeda( $dados[0]['total'], array('nozero' => true, 'places' => 0)), 'javascript:void(0)', array( 'onclick' => "consulta_geral_sm('{$dados[0]['codigo']}')" )) 
                    ?>
                </td>
			</tr>
            <?php $totalRegistros += $dados[0]['total']; ?>
        <?php endforeach ?>
        <?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
        <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
        <?php echo $this->Javascript->codeBlock('
            jQuery(document).ready(function(){
                $.tablesorter.addParser({
                    debug:true,
                    id: "brazil", 
                    is: function(s) { 
                        // return false so this parser is not auto detected 
                        // poderia ser detectado pelo simbolo do real R$
                        return false;
                    },
                    format: function(s) { 
                       s = s.replace(/\./g,"");
                       s = s.replace(/\,/g,".");
                       return $.tablesorter.formatFloat(s.replace(new RegExp(/[^0-9.-]/g),""));
                    }, 
                    type: "numeric"
                });

                jQuery("table.table").tablesorter({
                    headers: {
                        2: {sorter: "brazil"}
                    },
                    widgets: ["zebra"]
                });
            });', false);
        ?>
    
        <tfoot>
            <tr>
                <td colspan="2"><strong>Total:</strong></td>
                <td class='numeric'>
                    <?php echo $this->Html->link( $this->Buonny->moeda( $totalRegistros, array('nozero' => true, 'places' => 0)), 'javascript:void(0)', array( 'onclick' => "consulta_geral_sm('')" )) ?>
                </td>
            </tr>
        </tfoot>
    </table>
<?php else: ?>
    <?php if (isset($data) && count($data)==0): ?>
        <div class="alert">Nenhum dado foi encontrado.</div>
    <?php endif ?>
<?php endif ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php echo $this->Javascript->codeBlock("
    function consulta_geral_sm( tecn_codigo ) {
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');

        form.setAttribute('method', 'post');
        form.setAttribute('target', form_id);
        form.setAttribute('action', '/portal/relatorios_sm/listagem_consulta_geral_sm/' + Math.random());
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioSmConsulta][codigo_cliente]');
        field.setAttribute('value', '{$this->data['RelatorioEstatisticoSm']['codigo_cliente']}');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioSmConsulta][base_cnpj]');
        field.setAttribute('value', 'false');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioSmConsulta][tecn_codigo]');
        field.setAttribute('value', tecn_codigo);
        field.setAttribute('type', 'hidden');       
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioSmConsulta][data_inicial]');
        field.setAttribute('value', '{$this->data['RelatorioEstatisticoSm']['data_inicial']}');
        field.setAttribute('type', 'hidden');       
        form.appendChild(field);
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioSmConsulta][data_final]');
        field.setAttribute('value', '{$this->data['RelatorioEstatisticoSm']['data_final']}');
        field.setAttribute('type', 'hidden');       
        form.appendChild(field);    

        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioSmConsulta][codigo_status_viagem]');
        field.setAttribute('value', '7');
        field.setAttribute('type', 'hidden');       
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[RelatorioSmConsulta][tipo_view]');
        field.setAttribute('value', 'popup');
        field.setAttribute('type', 'hidden');       
        form.appendChild(field);

        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
        document.body.appendChild(form);
        form.submit();
    }");?>