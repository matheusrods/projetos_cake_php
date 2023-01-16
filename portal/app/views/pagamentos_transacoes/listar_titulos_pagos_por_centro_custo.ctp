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
            <?php else: ?>
            <strong>Empresa:</strong> Todas empresas 
            <?php endif; ?>
            
            <?php 
            $centro_custo = '';
            $centro_custo_desc = '';
            if(!empty($this->data['Tranpag']['ccusto'])): ?>
            <strong>Centro de Custo:</strong> <?php echo $this->data['Tranpag']['ccusto'] ." - ". $this->data['Tranpag']['centro_custo_desc']; ?>
            <?php 
            $centro_custo = $this->data['Tranpag']['ccusto'];
            $centro_custo_desc = $dados[0]['Grflux']['descricao'];
            else: ?>
            <strong>Centro de Custo:</strong> Todos
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <table class='table table-striped tablesorter'>
        <thead>
            <th><?php echo $this->Html->link('Cód.', 'javascript:void(0)') ?></th>
            <th><?php echo $this->Html->link('Centro de Custo', 'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Valor(R$)', 'javascript:void(0)') ?></th>
        </thead>

        <?php
        $total_valor = 0;
        $total_qtde  = 0;
        foreach ($dados as $item):
        ?>
            <tr>
                <td class="input-mini"><?php echo $this->Html->link($item['Grflux']['codigo'] , 'javascript:void(0)', array( 'onclick' => "listar_titulos_pagos_por_centro_custo_sub_codigo('Tranpag','{$item['Tranpcc']['ccusto']}','{$this->data['Tranpag']['centro_custo_desc']}','{$item['Grflux']['codigo']}','{$item['Grflux']['descricao']}','{$this->data['Tranpag']['data_inicial']}','{$this->data['Tranpag']['data_final']}','{$this->data['Tranpag']['grupo_empresa']}','{$this->data['Tranpag']['empresa']}')" )) ?></td>
                <td><?php echo $item['Grflux']['descricao'] ?></td>
                <td class='numeric'><?php echo $this->Buonny->moeda($item[0]['val_final']) ?></td>
            </tr>
            <?php
            $total_valor += $item[0]['val_final'];
            $total_qtde++;
            ?>
        <?php endforeach ?>
        <tfoot>
            <tr>
                <td><strong>Qtde.:</strong> </td>
                <td><?php echo $total_qtde; ?></td>
                <td class='numeric' colspan="2"><strong>Total:</strong> <?php echo $this->Buonny->moeda($total_valor) ?></td>
            </tr>
        </tfoot>
    </table>
<?php endif ?>


<?php $this->addScript($this->Buonny->link_js('pagamentos_transacoes')) ?>
<?php $this->addScript($this->Buonny->link_js('faturamento')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php $this->addScript($this->Buonny->link_js('search')) ?>
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
    });', false);
?>