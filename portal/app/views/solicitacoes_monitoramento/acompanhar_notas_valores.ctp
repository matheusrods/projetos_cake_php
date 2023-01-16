<div class='well'>
    <?php echo $this->BForm->create('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'acompanhar_notas_valores'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this) ?>
            <?php echo $this->BForm->input('sm_encerrada', array('class' => 'input-small', 'label' => false, 'placeholder' => 'Status SM', 'type' => 'hidden', 'value' => '3')); // SM's Encerradas ?>
            <?php //echo $this->Buonny->input_codigo_cliente($this) ?>
            <?php echo $this->Buonny->input_cliente_tipo($this, 0, $clientes_tipos); ?>
        </div>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end() ?>
</div>

<?php if(isset($cliente['Cliente'])): ?>
    <div class="well">
        <strong>Código:</strong> <?php echo $cliente['Cliente']['codigo']; ?> <strong>Cliente:</strong> <?php echo $cliente['Cliente']['razao_social']; ?>
    </div>
<?php endif; ?>

<?php if (isset($transportadoras)): ?>
    <table class='table table-striped tablesorter'>
        <thead>
            <th><?php echo $this->Html->link('Embarcador', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Transportador', 'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Qtd. Notas', 'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Qtd. SM', 'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Peso(Kg)', 'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Valor Gerenciado(R$)', 'javascript:void(0)') ?></th>
        </thead>

        <?php $total_notas    = 0; ?>
        <?php $total_sm       = 0; ?>
        <?php $total_peso     = 0; ?>
        <?php $total_valor_nf = 0; ?>
        
        <?php foreach ($transportadoras as $transportadora): ?>
            <tr>
                <td><?php echo empty($transportadora['Embarcador']['raz_social']) ? $transportadora['Transportador']['raz_social'] : $transportadora['Embarcador']['raz_social'] ?></td>
                <td><?php echo $transportadora['Transportador']['raz_social'] ?></td>
                <td class="numeric"><?php echo $transportadora['0']['QtdeFiscal'] ?></td>
                <td class="numeric"><?php echo $this->Html->link($transportadora['0']['QtdeSM'], "javascript:consulta_geral('{$transportadora['Embarcador']['codigo']}', '{$transportadora['Transportador']['codigo']}', '{$this->data['Recebsm']['data_inicial']}', '{$this->data['Recebsm']['data_final']}', 3)") ?></td>
                <td class='numeric'><?php echo $this->Buonny->moeda($transportadora['0']['Peso']) ?></td>
                <td class='numeric'><?php echo $this->Buonny->moeda($transportadora['0']['Valor_NF']) ?></td>
            </tr>
            <?php
            $total_notas    += $transportadora[0]['QtdeFiscal'];
            $total_sm       += $transportadora[0]['QtdeSM'];
            $total_peso     += $transportadora[0]['Peso'];
            $total_valor_nf += $transportadora[0]['Valor_NF'];
            ?>
        <?php endforeach ?>
        <tfoot>
            <tr>
                <td><strong>Total:</strong></td>
                <td></td>
                <td class='numeric'><?php echo $total_notas; ?></td>
                <td class='numeric'><?php echo $total_sm; ?></td>
                <td class='numeric'><?php echo $this->Buonny->moeda($total_peso); ?></td>
                <td class='numeric'><?php echo $this->Buonny->moeda($total_valor_nf) ?></td>
            </tr>
        </tfoot>
    </table>
<?php endif ?>

<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        $.tablesorter.addParser({
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