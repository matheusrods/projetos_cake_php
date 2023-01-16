<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
            <th><?php echo $this->Paginator->sort('Razão Social', 'razao_social') ?></th>
            <th><?php echo $this->Paginator->sort('Nome Fantasia', 'nome_fantasia') ?></th>
            <th colspan="3"><?php echo $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($clientes as $cliente) :?>
        <tr>
            <td class="input-mini"><?php echo $cliente['Cliente']['codigo'] ?></td>
            <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
            <td><?php echo $cliente['Cliente']['nome_fantasia'] ?></td>
            <td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
            <td class="pagination-centered">
                <?php echo $html->link('', array('controller' => 'clientes', 'action' => 'cliente_terceiros', $cliente['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Clientes')); ?>
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

<?php echo $javascript->codeblock('
	jQuery(document).ready(function() {});	
'); ?>



