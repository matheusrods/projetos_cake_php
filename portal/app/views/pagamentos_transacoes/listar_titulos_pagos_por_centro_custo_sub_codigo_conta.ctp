<div class='form-procurar'>
    <?php echo $this->element('pagamentos_transacoes/filtrar') ?>
</div>

<?php if(!empty($dados)): ?>
    
    <?php if(isset($nome_grupo) && !empty($nome_grupo)): ?>
        <div class="well">
            <strong>Data Inicial: </strong> <?php echo $this->data['Tranpag']['data_inicial'] ?> 
            <strong>Data Final: </strong><?php echo $this->data['Tranpag']['data_final'] ?> 
            <strong>Grupo:</strong> <?php echo $nome_grupo; ?> 
            <?php if(isset($this->data['Tranpag']['empresa']) && !empty($this->data['Tranpag']['empresa'])): ?>
            <strong class="nameEmpresa">Empresa:</strong> 
            <?php endif; ?>
            
            <?php if(!empty($this->data['Tranpag']['ccusto'])): ?>
                <strong>Centro de Custo:</strong> <?php echo $this->data['Tranpag']['ccusto'] ." - ". $this->data['Tranpag']['centro_custo_desc']; ?>
            <?php endif; ?>
            
            <?php if(isset($this->data['Tranpag']['sub_codigo']) && !empty($this->data['Tranpag']['sub_codigo'])): ?>
                <strong> - Sub Código:</strong> <?php echo $this->data['Tranpag']['sub_codigo'] ." - ". $this->data['Tranpag']['sub_codigo_desc']; ?>
            <?php endif; ?>
            
            <?php if(!empty($this->data['Tranpag']['codigo_conta'])): ?>
                <strong> - Conta:</strong> <?php echo $this->data['Tranpag']['codigo_conta'] ." - ". $this->data['Tranpag']['codigo_conta_desc']; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <table class='table table-striped tablesorter'>
        <thead>
            <th><?php echo $this->Html->link('Número', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('OR', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('SR', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('TD', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Emitente', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Emissão', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Vencto.', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Histórico', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('C. Custo', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Nº Red.', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Valor', 'javascript:void(0)') ?></th>
        </thead>

        <?php
        $total_valor = 0;
        $total_qtde  = 0;
        foreach ($dados as $item):
        ?>
            <tr>
                <td class="input-mini"><?php echo $item['Tranpag']['numero']; ?></td>
                <td class="input-mini"><?php echo $item['Tranpag']['ordem']; ?></td>
                <td class="input-mini"><?php echo $item['Tranpag']['serie']; ?></td>
                <td class="input-mini"><?php echo $item['Tranpag']['tipodoc']; ?></td>
                <td class=""><?php echo $item['Tranpag']['razao']; ?></td>
                <td class=""><?php echo $item[0]['data_emissao']; ?></td>
                <td class=""><?php echo $item[0]['data_vencimento']; ?></td>
                <td class=""><?php echo $item['Tranpag']['historico']; ?></td>
                <td class="input-small"><?php echo (empty($this->data['Tranpag']['ccusto'])) ? 'Todos' : $this->data['Tranpag']['ccusto'] ?></td>
                <td class=""><?php echo $item['Tranpcc']['numconta']; ?></td>
                <td class="numeric"><?php echo $this->Buonny->moeda($item['Tranpcc']['valor']); ?></td>
            </tr>
            <?php
            $total_valor += $item['Tranpcc']['valor'];
            $total_qtde++;
            ?>
        <?php endforeach ?>
        <tfoot>
            <tr>
                <td><strong>Qtde.:</strong> <?php echo $total_qtde; ?></td>
                <td class='numeric' colspan="12"><strong>Total:</strong> <?php echo $this->Buonny->moeda($total_valor) ?></td>
            </tr>
        </tfoot>
    </table>
<?php endif ?>


<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php $this->addScript($this->Buonny->link_js('pagamentos_transacoes')) ?>
<?php $this->addScript($this->Buonny->link_js('faturamento')) ?>
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
        setup_datepicker();
        
        $.tablesorter.addParser({
            id: 'brazil', 
            is: function(s) { 
                // return false so this parser is not auto detected 
                // poderia ser detectado pelo simbolo do real R$
                return false;
            },
            format: function(s) { 
               s = s.replace(/\./g,'');
               s = s.replace(/\,/g,'.');
               return $.tablesorter.formatFloat(s.replace(new RegExp(/[^0-9.-]/g),''));
            }, 
            type: 'numeric'
        });

        jQuery('table.table').tablesorter({
            headers: {
                12: {sorter: 'brazil'}
            },
            widgets: ['zebra']
        });
    });", false);
?>