<?php 
    // echo $paginator->options(array('update' => 'div.lista')); 
?>

<?php if(!empty($exames_sem_assinatura)): ?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Código Cliente</th>
				<th>Cliente</th>
				<th>Nome Fantasia</th>
				<th>Exame</th>				
			</tr>
		</thead>
		<tbody>
			<?php foreach($exames_sem_assinatura as $exames) :?>
				<?php foreach($exames as $exame) :?>
					<tr>
						<td><?php echo $exame['Cliente']['codigo'] ?></td>
						<td><?php echo $exame['Cliente']['razao_social'] ?></td>
						<td><?php echo $exame['Cliente']['nome_fantasia'] ?></td>
						<td><?php echo $exame['Servico']['descricao'] ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div class='row-fluid'>
		<div class='numbers span6'>
			<?php // echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
			<?php // echo $this->Paginator->numbers(); ?>
			<?php // echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
		</div>
		<div class='counter span6'>
			<?php // echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
		</div>
	</div>
<?php else: ?>
	<div class="alert">
        Nenhum dado encontrado.
    </div>
<?php endif;?>