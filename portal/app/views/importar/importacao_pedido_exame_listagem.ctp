<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<?php if ($codigo_status_importacao == StatusImportacao::SEM_PROCESSAR): ?>
	<span class='icon-warning-sign'></span> Alerta de inconsistência
<?php endif ?>
<table class="table" style='max-width: none; width:3000px'>
	<thead>
		<?php if ($codigo_status_importacao == StatusImportacao::SEM_PROCESSAR): ?>
			<th>Ação</th>
		<?php else: ?>
			<th>Status</th>
		<?php endif ?>
		<?php foreach ($titulos as $campo => $titulo): ?>
			<th><?= $this->Paginator->sort($titulo, $campo) ?></th>	
		<?php endforeach ?>
	</thead>
	<tbody>
		<?php foreach ($registros as $key => $registro): ?>
			<tr id='<?= $registro[0]['codigo'] ?>'>
				<td><?= $registro[0]['status_importacao'] ?></td>
				<?php foreach ($depara as $campo_planilha => $campo_tabela): ?>
					<td title='<?= $registro[0][$campo_tabela] ?>' class='alerta <?= $alertas[$key][$campo_planilha] ?>'>
						<?php if (isset($validacoes[$key][$campo_planilha])): ?>
							<span class='icon-warning-sign' title='<?= $validacoes[$key][$campo_planilha] ?>'></span>
						<?php endif ?>
						<?php if ($campo_planilha == 'cpf') :?>
							<?= Comum::formatarDocumento($registro[0][$campo_planilha]) ?>
						<?php else:?>
							<?= $registro[0][$campo_planilha] ?>
						<?php endif;?>
					</td>
				<?php endforeach ?>
			</tr>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<td>Total <?= $this->Paginator->counter(array('format' => '%count%')) ?></td>
			<td colspan='61'></td>
		</tr>
	</tfoot>
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
<style type="text/css">
	.alerta.inclusao, .alerta.inclusao:hover, .alerta.inclusao:nth-child(odd) {
		color: green
	}
	.alerta.alteracao, .alerta.alteracao:hover, .alerta.alteracao:nth-child(odd) {
		color: blue
	}
</style>
<?php echo $this->Js->writeBuffer(); ?>