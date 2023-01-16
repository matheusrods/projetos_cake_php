<div class='well'>
	<strong>Nome do arquivo: </strong><?= $atestados[0][0]['nome_arquivo'] ?>
	<strong>Data inclusão: </strong><?= $atestados[0]['ImportacaoAtestados']['data_inclusao'] ?>
</div>
<table class='table table-striped'>
	<thead>
		<th>Unidade</th>
		<th class='numeric input-small'>Atestados</th>
	</thead>
	<?php $total = 0 ?>
	<tbody>
		<?php foreach ($atestados as $key => $value): ?>
			<?php $total += $value[0]['qtd_atestados'] ?>
			<tr>
				<td><?= $value[0]['nome_empresa'] ?></td>
				<td class='numeric input-small'><?= $value[0]['qtd_atestados'] ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<td></td>
			<td class='numeric'><?= $total ?></td>
		</tr>
	</tfoot>
</table>
<?= $this->BForm->create('ImportacaoAtestados', array('url' => array('controller' => 'importar', 'action' => 'eliminar_importacao_atestado', $this->passedArgs[0], $this->passedArgs[1]))) ?>
<?= $this->BForm->submit('Excluir', array('div' => false, 'class' => 'btn btn-primary')) ?>
<?= $this->Html->link('Voltar',array('controller'=>'importar','action'=>'importar_atestado', $this->passedArgs[0]) , array('class' => 'btn')); ?>
<?= $this->BForm->end() ?>