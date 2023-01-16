<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php if( isset( $dados ) && !empty( $dados ) ): ?>
    <div id="show-chart" style="min-width: 400px; height: 400px; margin: 0 auto 50px">
        <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
        <?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
        <?php echo $this->Javascript->codeBlock($this->Highcharts->render($dados['eixo_x'], $dados['series'], array(
            'renderTo' => 'show-chart',
            'chart' => array('type' => 'pie'),
        ))); ?>
    </div>
<?php endif; ?>
<div class='well'>
    <strong>Embarcador: </strong><?php echo $cliente['Cliente']['razao_social'] ?>
    <strong>Transportador: </strong><?php echo $cliente_transportador['ClientEmpresa']['Raz_Social'];?>
    <strong>Gerador: </strong><?php echo $gerador['MGeradorOcorrencia']['descricao'];?>
    <strong>Período de: </strong><?php echo $this->data['Recebsm']['data_inicial']; ?><strong> até: </strong><?php echo $this->data['Recebsm']['data_final']; ?>
</div>
<?php if (isset($data)): ?>
	<table class='table table-striped tablesorter'>
        <thead>
            <th><?php echo $this->Html->link('Ocorrência', 'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Quantidade de RMA', 'javascript:void(0)') ?></th>
        </thead>
        <?php
        $totalRegistros = 0;
        foreach ($data as $dados):
        ?>
            <tr>
                <td><?php echo $dados[0]['ocorrencia'] ?></td>
                <td class='numeric'>
					<?php echo $this->Html->link($dados[0]['total'] , 'javascript:void(0)', array(
                        'onclick' => "estatistica_rma('".$dados[0]['codigo_cliente']."', '".$datas_selecionadas['data_inicial']."', '".$datas_selecionadas['data_final']."', '".$codigo_gerador_ocorrencia."', '".$dados[0]['codigo_rma']."', '".$embarcador."', '".$transportador."', '".$tipo_empresa."')"
                    )); ?>
                </td>
            </tr>
        <?php
            $totalRegistros += $dados[0]['total'];
        endforeach;
        ?>
        <tfoot>
            <tr>
                <td><strong>Total:</strong></td>
                <td class='numeric'><?php echo $totalRegistros; ?></td>
            </tr>
        </tfoot>
	</table>
<?php endif ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        jQuery(\'table.table\').tablesorter({
            sortList: [[1,1]]
        });
		init_combo_events();
		setup_datepicker();
    });', false);
?>


