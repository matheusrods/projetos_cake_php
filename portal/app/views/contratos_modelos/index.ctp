<?php /* Não gera o botão quando clica na ordenação */
      if (!$isAjax): ?>
    <div class='actionbar-right'>
        <?= $html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Modelo')) ?>
    </div>
<?php endif; ?>
<?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
?>

<div class="lista">
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
	<table class="table table-striped">
    	<thead>
    	   <tr>
    			<th><?php echo $this->Paginator->sort('descricao');?></th>
    			<th></th>
    	   </tr>
    	</thead>
    	<?php
    	$i = 0;
    	foreach ($contratosmodelos as $contratomodelo):
    		$class = null;
    		if ($i++ % 2 == 0) {
    			$class = ' class="altrow"';
    		}
    	?>
    	<tr<?php echo $class;?>>
    		<td><?php echo $contratomodelo['ContratoModelo']['descricao']; ?>&nbsp;</td>
    		<td>
                <?php echo $this->Html->link('', array('action' => 'mostrarcontrato', $contratomodelo['ContratoModelo']['codigo']), array('class' => 'icon-file')); ?>
    		</td>
    	</tr>
        <?php endforeach; ?>
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
</div>

<?php echo $this->Js->writeBuffer(); ?>
