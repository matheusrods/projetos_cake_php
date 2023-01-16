<div class="well">
    <strong>Evento: </strong><?php echo $evento; ?> - <strong>Status: </strong><?php echo ($espa_sla == 0) ? 'Fora SLA' : 'Dentro SLA'; ?>
</div>

<div class="lista">   

    <table class="table table-striped table-bordered tablesorter" id="info-eventos">

        <thead>
            <tr>
                <th><?php echo $this->Html->link('SM', 'javascript:void(0)') ?></th>
                <th><?php echo $this->Html->link('Placa', 'javascript:void(0)') ?></th>
                <th><?php echo $this->Html->link('Data Ocorrência', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Minutos em Atraso"><?php echo $this->Html->link('M. Atraso', 'javascript:void(0)') ?></th>
                <th><?php echo $this->Html->link('Estação', 'javascript:void(0)') ?></th>
                <th><?php echo $this->Html->link('Operador', 'javascript:void(0)') ?></th>
                <th><?php echo $this->Html->link('Cliente', 'javascript:void(0)') ?></th>
                <th class="numeric"><?php echo $this->Html->link('Qtd. Ocorrências', 'javascript:void(0)') ?></th>                
            </tr>
        </thead>

        <tbody>

            <?php $total_ocorrencias = 0; ?>

            <?php if (isset($dados) && !empty($dados)): ?>                

                <?php foreach ($dados as $key => $value): ?>
                <tr>
                    <td><?= $this->Buonny->codigo_sm((isset($value[0]['Recebsm']['sm']) ? $value[0]['Recebsm']['sm'] : '')); ?></td>
                    <td>
                        <?php
                        echo $this->Buonny->placa(
                                (isset($value[0]['Recebsm']['placa']) ? $value[0]['Recebsm']['placa'] : ''), (isset($value[0][0]['dta_inc']) ? $value[0][0]['dta_inc'] : ''), (isset($value[0][0]['dta_fim']) ? $value[0][0]['dta_fim'] : '')
                        )
                        ?>
                    </td>
                    <td><?php echo date('d/m/Y H:i:s', strtotime($value[0]['data_cadastro'])) ?></td>
                    <td class="numeric"><?php echo number_format($value[0]['minutos_em_atraso'], 0, ",", "."); ?></td>
                    <td><?php echo $value[0]['estacao'] ?></td>
                    <td><?php echo (isset($value[0][0]['operador']) ? $value[0][0]['operador'] : '') ?></td>
                    <td><?php echo (isset($value[0][0]['cliente']) ? $value[0][0]['cliente'] : '') ?></td>
                    <td class="numeric"><?php echo $value[0]['qtd_ocorrencia_do_evento'] ?></td>
                </tr>

                <?php $total_ocorrencias += $value[0]['qtd_ocorrencia_do_evento']; ?>

                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>        

        <tfoot>
            <tr>
                <td colspan="7"><strong>Total:<strong></td>
                            <td class="numeric"><strong><?php echo $total_ocorrencias; ?><strong></td>
                                        </tr>
                                        </tfoot>

                                        </table>

                                        </div>

                                        <div class="form-actions">  
                                            <?= $html->link('Voltar', array('action' => 'situacao_monitoramento'), array('class' => 'btn')); ?>
                                        </div>

                                        <?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
                                        <?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
                                        <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
                                        <?php $this->addScript($this->Buonny->link_js('search')) ?>
                                        <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ 
    jQuery(\'table.table\').tablesorter({sortList: [[2,1]]}); 
});', false); ?>
