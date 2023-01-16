<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php if (!empty($notafis)): ?>
    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
    <table class="table table-striped table-bordered tablesorter">
        <thead>
            <tr>
                <th title="Mês"><?= $this->Html->link('Mês', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Emails Enviados"><?= $this->Html->link('Quantidade de Emails Enviados', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Notas fiscais Emitidas"><?= $this->Html->link('Quantidade de Notas fiscais Emitidas', 'javascript:void(0)') ?></th>
				<th class="numeric" title="Quantidade de Notas fiscais Canceladas"><?= $this->Html->link('Quantidade de Notas fiscais Canceladas', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Clientes"><?= $this->Html->link('Quantidade de Clientes', 'javascript:void(0)') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $qtd_enviados = 0; ?>
            <?php $qtd_nfs = 0; ?>
			<?php $qtd_nfs_canceladas = 0; ?>
            <?php $qtd_clientes = 0; ?>
            <?php if ($notafis): ?>
                <?php foreach ($notafis as $notafiscal): ?>
                    <tr>
                        <td><span style="display:none"><?= $notafiscal['Notafis']['ano_mes']; ?></span><?= Comum::anoMes($notafiscal['Notafis']['ano_mes']); ?></td>
                        <td class="numeric"><?= $notafiscal['Notafis']['qtd_envio']; ?></td>
                        <td class="numeric"><?php echo $notafiscal['Notafis']['qtd_nf']; ?></td>
                        <td class="numeric"><?php echo $notafiscal['Notafis']['qtd_nf_canceladas']; ?></td>
                        <td class="numeric"><?= $notafiscal['Notafis']['qtd_cliente']; ?></td>
                    </tr>
                    <?php $qtd_enviados += $notafiscal['Notafis']['qtd_envio']; ?>
                    <?php $qtd_nfs += $notafiscal['Notafis']['qtd_nf']; ?>
					<?php $qtd_nfs_canceladas += $notafiscal['Notafis']['qtd_nf_canceladas']; ?>
                    <?php $qtd_clientes += $notafiscal['Notafis']['qtd_cliente']; ?>
                <?php endforeach; ?>
            <?php else: ?>
                    <tr>
                        <td colspan="4">Sem dados para exibição</td>
                    </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <th></th>
            <th class='numeric'><?php echo $qtd_enviados; ?></th>
            <th class='numeric'><?php echo $qtd_nfs; ?></th>
			<th class='numeric'><?php echo $qtd_nfs_canceladas; ?></th>
            <th class='numeric'><?php echo $qtd_clientes; ?></th>
        </tfoot>
    </table>
    <?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter()")) ?>
    
    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
    <?php echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
        'renderTo' => 'grafico',
        'chart' => array('type' => 'line'),
        'yAxis' => array('title' => ''),
        'xAxis' => array('labels' => array('rotation' => -10, 'y' => 20), 'gridLineWidth' => 1),
        'tooltip' => array('formatter' => 'this.y'),
    ))); ?>
<?php endif; ?>