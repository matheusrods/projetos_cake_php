<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar'));?>
</div>
<br/>
<?php if(isset($listagem) && !empty($listagem)):?>
	<?php
	    echo $paginator->options(array('update' => 'div.lista'));
	?>
	<table class='table table-striped' style='max-width:none;white-space:nowrap'>
		<thead>
			<th><?php echo $this->Paginator->sort('Descrição', 'descricao') ?></th>
			<th class="input-mini numeric">&nbsp;</th>
		</thead>
		<tbody>
			<?php foreach ($listagem as $dado): ?>
				<tr style='word-wrap:none'>
					<td><?= $dado['Diretoria']['descricao'] ?></td>
					<td>
						<?php if($dado['Diretoria']['ativo']):?>
							<?= $this->Html->link('', array('action' => 'editar', $dado['Diretoria']['codigo'], rand()), array('title' => 'Editar', 'class' => 'icon-edit')) ?>	
							<?= $this->Html->link('', array('action' => 'inativar_ativar',$dado['Diretoria']['codigo'],'0' , rand()), array('title' => 'Inativar', 'class' => 'icon-random'));?>
							<span class="badge-empty badge badge-success" title="Ativo"></span>
						<?php else:?>
							<?= $this->Html->link('', array('action' => 'inativar_ativar',$dado['Diretoria']['codigo'],'1', rand()), array('title' => 'Ativar', 'class' => 'icon-random'));?>
							<span class="badge-empty badge badge-important" title="Inativo"></span>
						<?php endif;?>	
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