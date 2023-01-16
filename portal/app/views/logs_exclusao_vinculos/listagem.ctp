<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();    
?>
<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th ><?php echo $this->Paginator->sort('Código', 'apelido') ?></th>
            <th ><?php echo $this->Paginator->sort('Razão Social', 'TipoOperacao.descricao') ?></th>
            <th ><?php echo $this->Paginator->sort('CPF', 'Profissional.codigo_documento') ?></th>
            <th ><?php echo $this->Paginator->sort('Profissional', 'Profissional.codigo_documento') ?></th>
            <th ><?php echo $this->Paginator->sort('Data Exclusão', 'data_inicio') ?></th>
           <th ><?php echo $this->Paginator->sort('Usuario', 'TipoOperacao.descricao') ?></th>
            <th ><?php echo $this->Paginator->sort('Categoria', 'TipoOperacao.descricao') ?></th>
            <th><?php echo $this->Paginator->sort('Produto', 'Produto.descricao') ?></th>
         </tr>
    </thead>

<?php foreach ($lista as $logsexclusaovinculo): ?>
    <tr>
        <td><?php echo $logsexclusaovinculo[0]['codigo']?></td>
        <td><?php echo $logsexclusaovinculo[0]['descricao'] ?></td>
        <td><?php echo COMUM::formatarDocumento($logsexclusaovinculo[0]['Profissional__0']) ?></td>
        <td><?php echo $logsexclusaovinculo[0]['Profissional__1'] ?></td> 
        <td><?php echo date('d/m/Y H:i:s', strtotime(str_replace('/', '-', $logsexclusaovinculo[0]['data_exclusao']))); ?></td>
        <td><?php echo $buonny->documento($logsexclusaovinculo[0]['Usuario__2']) ?></td>
        <td><?php echo $logsexclusaovinculo[0]['ProfissionalTipo__3'] ?></td>
        <td><?php echo $logsexclusaovinculo[0]['Produto__4'] ?></td>  
        
    </tr>
<?php endforeach; ?>
    <tfoot><tr><td colspan="10"><strong>Total </strong><?php echo $count;?></td></tr></tfoot>
</table>

<div class='row-fluid'>
    <div class='numbers span6'>
        <?php if($this->Paginator->counter('{:pages}') > 1): ?>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        <?php endif; ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>