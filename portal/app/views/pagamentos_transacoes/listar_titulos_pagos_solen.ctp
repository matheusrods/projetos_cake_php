<div class='form-procurar'>
    <?php echo $this->element('pagamentos_transacoes/filtrar_solen') ?>
</div>

    <?php if(isset($nome_grupo) && !empty($nome_grupo)): ?>
        <div class="well">
            <strong>Grupo:</strong> <?php echo $nome_grupo; ?> 
            <?php if(isset($this->data['Tranpag']['empresa']) && !empty($this->data['Tranpag']['empresa'])): ?>
            <strong class="nameEmpresa">Empresa:</strong> 
            <?php else: ?>
            <strong>Empresa:</strong> Todas empresas
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <table class='table table-striped tablesorter'>
        <thead>
            <th><?php echo $this->Html->link('Cód.', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Centro de Custo', 'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Meta(R$)', 'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Realizado(R$)', 'javascript:void(0)') ?></th> 
            <th class='numeric'><?php echo $this->Html->link('% Percentual realizado', 'javascript:void(0)') ?></th> 
        </thead>
        <?php
        $total_valor = 0;
        $total_valor_meta = 0;
        $total_qtde  = 0;
        if( !empty($dados) ):
        foreach ($dados as $item):

            ?>
            <?php 
                $percentual_dentro_meta = $item['0']['meta_total'] ? round(($item['0']['realizado_total']/$item['0']['meta_total'])*100, 3) : NULL;
                $class = ($percentual_dentro_meta && $percentual_dentro_meta > 100 ? 'negative_value' : '');
            ?>
            <tr>
               <td class="input-mini"><?php echo $this->Html->link($item['CentroCusto']['codigo'],
                 "javascript:listar_titulos_pagos_por_centro_custo('Tranpag','{$item['CentroCusto']['codigo']}','{$item['CentroCusto']['descricao']}','{$this->data['Tranpag']['ano']}','{$this->data['Tranpag']['mes']}','{$this->data['Tranpag']['grupo_empresa']}','{$this->data['Tranpag']['empresa']}','{$item['CentroCusto']['descricao']}')")?>
                </td>
                <td><?php echo $item['CentroCusto']['descricao'] ?></td>
                <td class='numeric'> <?php echo $this->Buonny->moeda($item[0]['meta_total'], array('nozero' => true) ) ?></td>
                <td class='numeric'> <?php echo $this->Buonny->moeda($item[0]['realizado_total'], array('nozero' => true) ) ?></td>
                <td class='numeric <?=$class?>'>
                    <?php echo $buonny->moeda( $percentual_dentro_meta, array('nozero' => true)) ?>
                </td>                
            </tr>
            <?php
            $total_valor += $item[0]['realizado_total'];
            $total_valor_meta += $item[0]['meta_total'];
            $total_qtde++;
            ?>
        <?php endforeach ?>
        <?php endif; ?>
        <tfoot>
            <tr>
                <td><strong>Qtde.:</strong></td>
                <td><?php echo $total_qtde; ?></td>
                <td class='numeric'><strong>Total: </strong><?php echo $this->Buonny->moeda($total_valor_meta);?></td>
                <td class='numeric'><strong>Total: </strong><?php echo $this->Buonny->moeda($total_valor);?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

<?php $this->addScript($this->Buonny->link_js('pagamentos_transacoes')) ?>
<?php $this->addScript($this->Buonny->link_js('faturamento')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php echo $this->Javascript->codeBlock('   
    jQuery(document).ready(function(){
        setup_datepicker();
        
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
    });   
    
    ', false);
?>