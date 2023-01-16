<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?= $this->Paginator->sort('Código', 'codigo') ?></th>
            <th><?= $this->Paginator->sort('Nome', 'nome') ?></th>
            <th colspan="2"><?= $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($seguradoras as $seguradora): ?>
        <tr>
            <td class="input-mini">
                <?= $seguradora['Seguradora']['codigo'] ?>
            </td>
            <td>
                <?= $seguradora['Seguradora']['nome'] ?>
            </td>
            <td>
                <?= $buonny->documento($seguradora['Seguradora']['codigo_documento']) ?>
            </td>
            <td class="pagination-centered">
                <?php if($destino == "usuarios"): ?>
                    <?= $html->link('', array('controller' => 'usuarios', 'action' => 'por_seguradora', $seguradora['Seguradora']['codigo']), array('class' => 'icon-wrench', 'title' => 'Usuários da Seguradora')) ?>
                <?php else: ?>
                    <?= $html->link('', array('action' => 'editar', $seguradora['Seguradora']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
                <?php endif; ?>  
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
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