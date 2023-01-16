<?php if($permite):?>
	<div class='actionbar-right'>
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar'));?>
	</div>
	<br/>
	<?php if(isset($listagem) && !empty($listagem)):?>
		<?php
		    echo $paginator->options(array('update' => 'div.lista'));
		?>
		<table class='table table-striped' style='max-width:none'>
			<thead>
				<th>Usuário</th>
				<th>Data hora extra</th>
				<th>Usuário inclusão</th>
				<th>Motivo Cliente</th>
				<th>Motivo da hora extra</th>
				<th colspan="2">&nbsp;</th>
			</thead>
			<tbody>
				<?php foreach ($listagem as $dado): ?>
					<tr>
						<td><?= $dado['Usuario']['nome']?></td>
						<td><?= substr($dado['AutorizacaoHoraExtra']['data_hora_extra'],0,10)?></td>
						<td><?= $dado['Gestor']['nome']?></td>
						<td><?= ($dado['AutorizacaoHoraExtra']['motivo_cliente']) ? 'Sim' : 'Não'?></td>
						<td><?= $dado['AutorizacaoHoraExtra']['motivo_hora_extra']?></td>
	        			<td class="numeric"><?php echo $html->link('', array('controller' => $this->name, 'action' => 'editar', $dado['AutorizacaoHoraExtra']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')); ?></td>
	        			<td><?php echo $html->link('', array('controller' => $this->name, 'action' => 'excluir', $dado['AutorizacaoHoraExtra']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Confirma exclusão?'); ?></td>
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
<?php else:?>
		<div class="alert">Somente o gestor de registro de ponto tem acesso ao sistema.</div>
<?php endif;?>