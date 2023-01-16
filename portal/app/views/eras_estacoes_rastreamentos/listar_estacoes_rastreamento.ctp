<?php
    echo $paginator->options(array('update' => 'div.lista'));
?>
<div class="lista">
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th><?php echo $this->Paginator->sort('Descrição','eras_descricao') ?></th>
				<th><?php echo $this->Paginator->sort('Ramal','eras_ramal') ?></th>
				<th><?php echo $this->Paginator->sort('Estação Logistica','eras_descricao_logistico') ?></th>
				<th class="input-mini">&nbsp;</th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach ($lista as $dado): ?>
	            <tr>
		            <td><?= $dado['TErasEstacaoRastreamento']['eras_descricao']?></td>
					<td><?= $dado['TErasEstacaoRastreamento']['eras_ramal']?></td>
					<td><?= $dado['TErasEstacaoRastreamento2']['eras_descricao']?></td>
					<td><?= $this->Html->link('', array('action' => 'editar', $dado['TErasEstacaoRastreamento']['eras_codigo']), array('title' => 'Editar', 'class' => 'icon-edit')) ?></td>
	            </tr>
	        <?php endforeach ?>
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
</div>
<?php echo $this->Js->writeBuffer(); ?>