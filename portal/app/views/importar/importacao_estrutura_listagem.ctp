<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<?php if ($codigo_status_importacao == StatusImportacao::SEM_PROCESSAR): ?>
	<span class='alerta inclusao'>Campos em verde serão incluídos</span> <span class='alerta alteracao'>Campos em azul serão atualizados</span> <span class='alerta ambos'>Campos em laranja serão incluídos e atualizados</span> <span class='icon-warning-sign'></span> Alerta de informação inválida
<?php endif ?>
<table class="table" style='max-width: none; width:10000px'>
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
				<?php if ($codigo_status_importacao == StatusImportacao::SEM_PROCESSAR): ?>
					<td><?= $registro[0]['acao_funcionario'] ?></td>
				<?php else: ?>
					<td><?= $registro[0]['status_importacao'] ?></td>
				<?php endif ?>
				<?php foreach ($depara as $campo_planilha => $campo_tabela): ?>
					<td title='<?= $registro[0][$campo_tabela] ?>' class='alerta <?= $alertas[$key][$campo_planilha] ?>'>
						<?php if (isset($validacoes[$key][$campo_planilha])): ?>
							<span class='icon-warning-sign' title='<?= $validacoes[$key][$campo_planilha] ?>'></span>
						<?php endif ?>
						<?= $registro[0][$campo_planilha] ?>
					</td>
				<?php endforeach ?>
			</tr>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<td>Total <?= $this->Paginator->counter(array('format' => '%count%')) ?></td>
			<td colspan='66'></td>
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
	.alerta.ambos, .alerta.ambos:hover, .alerta.ambos:nth-child(odd) {
		color: #CD6600
	}
</style>
<?php echo $this->Js->writeBuffer(); ?>
