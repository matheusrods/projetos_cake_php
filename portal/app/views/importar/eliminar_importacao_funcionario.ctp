<div class='well'>
	<strong>Nome do arquivo: </strong><?= $estrutura[0]['ImportacaoEstrutura']['nome_arquivo'] ?>
	<strong>Data inclusão: </strong><?= $estrutura[0]['ImportacaoEstrutura']['data_inclusao'] ?>
</div>
<table class='table table-striped'>
	<thead>
		<th>Unidade</th>
		<th class='numeric input-small'>Funcionários</th>
	</thead>
	<?php $total = 0 ?>
	<tbody>
		<?php foreach ($estrutura as $key => $value): ?>
			<?php $total += $value[0]['qtd_funcionarios'] ?>
			<tr>
				<td><?= $value['RegistroImportacao']['nome_alocacao'] ?></td>
				<td class='numeric input-small'><?= $value[0]['qtd_funcionarios'] ?></td>
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
<?= $this->BForm->create('ImportacaoEstrutura', array('url' => array('controller' => 'importar', 'action' => 'eliminar_importacao_funcionario', $this->passedArgs[0], $this->passedArgs[1], $referencia, $terceiros_implantacao))) ?>
<?= $this->BForm->submit('Excluir', array('div' => false, 'class' => 'btn btn-primary')) ?>
<?= $this->Html->link('Voltar',array('controller'=>'importar','action'=>'importar_funcionario', $this->passedArgs[0], $referencia, $terceiros_implantacao) , array('class' => 'btn')); ?>
<?= $this->BForm->end() ?>