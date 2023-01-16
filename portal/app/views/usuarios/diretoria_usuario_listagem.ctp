<?php if(isset($listagem) && !empty($listagem)):?>
	<?php
	    echo $paginator->options(array('update' => 'div.lista'));
	?>
	<table class='table table-striped' style='max-width:none;white-space:nowrap'>
		<thead>
			<th><?php echo $this->Paginator->sort('Gestor', 'Usuario.nome') ?></th>
			<th><?php echo $this->Paginator->sort('Diretoria', 'Diretoria.descricao') ?></th>
			<th class="input-mini numeric">&nbsp;</th>
		</thead>
		<tbody>
			<?php foreach ($listagem as $dado): ?>
				<tr style='word-wrap:none'>
					<td><?= $dado['Usuario']['nome'] ?></td>				
					<td><?= $dado['Diretoria']['descricao'] ?></td>			
					<td class="numeric"><?php
                        echo $html->link('', array('action' => 'diretoria_usuario_editar', $dado['Usuario']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar'));
                    ?>
                    </td>	
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
	<?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
	<div class="alert">Nenhum registro encontrado</div>	
<?php endif;?>