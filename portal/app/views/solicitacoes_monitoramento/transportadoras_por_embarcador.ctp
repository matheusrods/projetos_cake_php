<div class='well'>
    <?php echo $this->BForm->create('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'transportadoras_por_embarcador'))) ?>
    <div class="row-fluid inline">
        <?php echo $this->Buonny->input_periodo($this) ?>
        <?php echo $this->BForm->input('sm_encerrada', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Status SM', 'type' => 'hidden', 'value' => '3')); // SM's Encerradas ?>
        <?php echo $this->Buonny->input_cliente_tipo($this, 1, $clientes_embarcadores) ?>
    </div>
    <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end() ?>
</div>

<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>

<?php if (isset($transportadoras) && count($transportadoras) > 0): ?>
    <div class="well">
        <strong>Cliente: </strong><?php echo $razao_social; ?>
    </div>
	<table class='table table-striped tablesorter'>
            <thead>
                <th><?php echo $this->Html->link('Código', 'javascript:void(0)') ?></th>
                <th><?php echo $this->Html->link('Transportadora', 'javascript:void(0)') ?></th>
                <th class='numeric'><?php echo $this->Html->link('Qtd. SM', 'javascript:void(0)') ?></th>
                <th class='numeric'><?php echo $this->Html->link('Qtd. RMA', 'javascript:void(0)') ?></th>
            </thead>
            <?php
            $total_transp = 0;
            $total_sm = $total_rma = 0;
            foreach ($transportadoras as $transportadora):
            ?>
            <tr>
                <?php if (empty($transportadora['0']['razao_social'])): ?>
                    <td><?php echo $transportadora['Recebsm']['cliente_embarcador'] ?></td>
                    <td><?php echo $transportadora[0]['razao_social_embarcador'] ?></td>
                    <td class="numeric"><?php echo $this->Html->link($transportadora[0]['qtd_sm'], 'javascript:void(0)', array('onclick' => "listar_acompanhamento_sms('Recebsm','{$this->data['Recebsm']['codigo_embarcador']}','{$this->data['Recebsm']['cliente_embarcador']}','{$transportadora['ClienteTransportador']['codigo_cliente']}','{$transportadora['Recebsm']['cliente_transportador']}','{$this->data['Recebsm']['data_inicial']}','{$this->data['Recebsm']['data_final']}','{$this->data['Recebsm']['sm_encerrada']}','')")) ?></td>
                <?php else: ?>
                    <td><?php echo $transportadora['Recebsm']['cliente_transportador'] ?></td>
                    <td><?php echo $transportadora[0]['razao_social'] ?></td>
                    <td class="numeric"><?php echo $this->Html->link($transportadora[0]['qtd_sm'], 'javascript:void(0)', array('onclick' => "listar_acompanhamento_sms('Recebsm','{$this->data['Recebsm']['codigo_embarcador']}','{$this->data['Recebsm']['cliente_embarcador']}','{$transportadora['ClienteTransportador']['codigo_cliente']}','{$transportadora['Recebsm']['cliente_transportador']}','{$this->data['Recebsm']['data_inicial']}','{$this->data['Recebsm']['data_final']}','{$this->data['Recebsm']['sm_encerrada']}','')")) ?></td>
                <?php endif ?>
                
                <?php if (empty($transportadora['0']['razao_social'])): ?>
                    <td class='numeric'><?= $this->Html->link($transportadora[0]['qtd_rma'] , 'javascript:void(0)', array( 'onclick' => "estatistica_rma_por_transportadora('{$this->data['Recebsm']['data_inicial']}','{$this->data['Recebsm']['data_final']}','{$this->data['Recebsm']['codigo_embarcador']}', '{$transportadora['Recebsm']['cliente_embarcador']}','')" )) ?></td>
                <?php else: ?>
                    <td class='numeric'><?= $this->Html->link($transportadora[0]['qtd_rma'] , 'javascript:void(0)', array( 'onclick' => "estatistica_rma_por_transportadora('{$this->data['Recebsm']['data_inicial']}','{$this->data['Recebsm']['data_final']}','{$this->data['Recebsm']['codigo_embarcador']}', '{$this->data['Recebsm']['cliente_embarcador']}','{$transportadora['Recebsm']['cliente_transportador']}')" )) ?></td>
                <?php endif ?>
            </tr>
            <?php
                $total_transp++;
                $total_sm += $transportadora[0]['qtd_sm'];
                $total_rma += $transportadora[0]['qtd_rma'];
            ?>
            <?php endforeach ?>
            <tfoot>
                <tr>
                    <td><strong>Total:</strong></td>    
                    <td><?php echo $total_transp ?></td>
                    <td class='numeric'><?php echo $total_sm; ?></td>
                    <td class='numeric'><?php echo $total_rma; ?></td>
                </tr>
            </tfoot>
	</table>
        
        <?php if (count($transportadoras) > 0): ?>
        <?php echo $this->Javascript->codeBlock('
            jQuery(document).ready(function(){
                jQuery("table.table").tablesorter({
                    sortList: [[2,1]]
                });
            });', false);
        ?>    
        <?php endif ?>

<?php endif ?>

<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>