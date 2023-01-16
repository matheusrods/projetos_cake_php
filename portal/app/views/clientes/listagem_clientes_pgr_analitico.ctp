<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini">Código</th>
            <th>Cliente</th>
            <th>Seguradora</th>
            <th>Corretora</th>
            <th>Gestor</th>
            <th>Filial</th>
            <th>Gestor NPE</th>
            <th>Validade da Apolice</th>
            <th>Possui Regra de Aceite</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($clientes_pgr as $cliente):;?>
        <tr>
            <td class="input-mini"><?php echo $cliente[0]['codigo_cliente'] ?></td>
            <td><?php echo $cliente[0]['cliente'] ?></td>
            <td><?php echo $buonny->documento($cliente[0]['seguradora']) ?></td>
            <td><?php echo $buonny->documento($cliente[0]['corretora']) ?></td>
            <td><?php echo $buonny->documento($cliente[0]['gestor']) ?></td>
            <td><?php echo $buonny->documento($cliente[0]['filial']) ?></td>
            <td><?php echo $buonny->documento($cliente[0]['gestor_npe']) ?></td>
            <td><?php echo AppModel::DbDateToDate($cliente[0]['validade_apolice']) ?></td>
            <td><?php echo ($cliente[0]['regra_de_aceite']) ? 'Sim' : 'Não';?></td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
    <tfoot>
        <tr>
            <td colspan = "13"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Cliente']['count']; ?></td>
        </tr>
    </tfoot>    
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
    	<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
        <?php echo $this->Paginator->numbers(); ?>
    	<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>