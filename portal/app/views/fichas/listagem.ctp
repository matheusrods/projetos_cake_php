<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
    // Codigo | Razao Social | Produto | Validade da Ficha | Data cadastro | Status
?>
<table>
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Código', 'Ficha.codigo') ?></th>
            <th><?= $this->Paginator->sort('Código', 'Cliente.codigo') ?></th>
            <th><?= $this->Paginator->sort('Razão Social', 'Cliente.razao_social') ?></th>
            <th><?= $this->Paginator->sort('Produto', 'Produto.codigo') ?></th>
            <th><?= $this->Paginator->sort('Data cadastro', 'Ficha.data_inclusao') ?></th>
            <th><?= $this->Paginator->sort('Validade', 'Ficha.data_validade') ?></th>
            <th><?= $this->Paginator->sort('Status', 'Status.codigo') ?></th>
            <th>
                 <?php if ($destino == 'fichas'): ?>
                    Editar
                 <?php else: ?>
                 <?php endif; ?>
            </th>
        </tr>
    </thead>
    <!-- $buonny->documento($ficha['Ficha']['codigo_produto']) -->
    <tbody>
        <?php foreach ($fichas as $ficha): ?>
        <tr>
            <td><?= $ficha['Ficha']['codigo'] ?></td>
            <td><?= $ficha['Cliente']['codigo'] ?></td>
            <td><?= $ficha['Cliente']['razao_social'] ?></td>
            <td><?= $ficha['Produto']['descricao'] ?></td>
            <td><?= $ficha['Ficha']['data_inclusao'] ?></td>
            <td><?= $ficha['Ficha']['data_validade'] ?></td>
            <td><?= $ficha['Status']['descricao'] ?></td>
            <td>
                <?php if ($destino == 'fichas'): ?>
                    <?php if ($podeAlterarStatus) : ?>
                        <?= $html->link('Editar', array('action' => 'atualizacao_profissional', $ficha['Ficha']['codigo']), array('class' => 'edit', 'onclick' => 'return open_dialog(this, "Alteração de Status", 700)')) ?>
                    <?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
<div class='paginador'>

<?php if($this->Paginator->numbers()): ?>
    <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
    <div class='numbers'>
        <?php echo $this->Paginator->numbers(); ?>
    </div>    
    <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
<?php endif; ?>

    <div class='counter'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>