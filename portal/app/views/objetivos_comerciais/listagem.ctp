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
			<th><?php echo $this->Paginator->sort('Mês', 'mes') ?></th>
			<th><?php echo $this->Paginator->sort('Ano', 'ano') ?></th>
			<th><?php echo $this->Paginator->sort('Filial', 'filial_descricao') ?></th>
			<th><?php echo $this->Paginator->sort('Gestor', 'nome_gestor') ?></th>
			<th><?php echo $this->Paginator->sort('Produto', 'produto') ?></th>
			<th class="numeric"><?php echo $this->Paginator->sort('Visitas', 'visitas_objetivo') ?></th>
			<th class="numeric"><?php echo $this->Paginator->sort('Faturamento (R$)', 'faturamento_objetivo') ?></th>
			<th class="numeric"><?php echo $this->Paginator->sort('Novos Clientes', 'novos_clientes_objetivo') ?></th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</thead>
		<tbody>
			<?php foreach ($listagem as $dado): ?>
				<tr style='word-wrap:none'>
					<td><?= COMUM::anoMes('2014/'.$dado[0]['mes']) ?></td>
					<td><?= $dado[0]['ano'] ?></td>
					<td><?= $dado[0]['filial_descricao'] ?></td>
					<td><?= $dado[0]['nome_gestor'] ?></td>
					<td><?= $dado[0]['produto_descricao'] ?></td>
					<td class="numeric"><?= $dado[0]['visitas_objetivo'] ?></td>
					<td class="numeric"><?= $this->Buonny->moeda($dado[0]['faturamento_objetivo']) ?></td>
					<td class="numeric"><?= $dado[0]['novos_clientes_objetivo'] ?></td>
					<td class="numeric"><?php echo $html->link('', array('controller' => 'objetivos_comerciais', 'action' => 'editar', $dado[0]['codigo_id']), array('class' => 'icon-edit', 'title' => 'Editar')); ?></td>
        			<td><?php echo $html->link('', array('controller' => 'objetivos_comerciais', 'action' => 'excluir', $dado[0]['codigo_id']), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Confirma exclusão?'); ?></td>
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