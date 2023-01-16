<?php if (!empty($dados)):?>
<div class='well'>
    <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
</div>
<table class="table table-striped" style='width:3000px;max-width:none;'>
    <thead>
        <tr>
			<th>Pedido</th>
			<th>Cliente Matrícula</th>
			<th>Unidade Alocação</th>			
			<th>Cidade Unidade</th>
			<th>Estado Unidade</th>
			<th>Funcionário</th>
			<th>Setor</th>
			<th>Cargo</th>
			<th>CPF</th>
			<th>Matrícula</th>
			<th>Exame</th>
			<th>Tipo de exame ocupacional</th>
			<th>Credenciado</th>
			<th>Cidade Credenciado</th>
			<th>Estado Credenciado</th>
			<th>Respondido Lyn</th>
			<th>Data Emissão Pedido</th>
			<th>Data Realização do Exame</th>
			<th>Data Baixa do Exame</th>
			<th>Fornecedor Particular</th>
			<th>Resultado do Exame</th>
		</tr>
	</thead>
	<tbody>
		<?php $total = 0 ?>
		<?php foreach($dados as $key => $value) : ?>
			<?php //$total += 1 ?>
			<tr>
				<td><?= $value[0]['codigo'] ?></td>
				<td><?= $value[0]['cliente'] ?></td>
				<td><?= $value[0]['unidade_nome_fantasia'] ?></td>
				<td><?= $value[0]['cliente_cidade'] ?></td>
				<td><?= $value[0]['cliente_estado'] ?></td>
				<td><?= $value[0]['funcionario'] ?></td>
				<td><?= $value[0]['setor_descricao'] ?></td>
				<td><?= $value[0]['cargo_descricao'] ?></td>
				<td><?= $value[0]['cpf'] ?></td>
				<td><?= $value[0]['matricula'] ?></td>
				<td><?= $value[0]['exame_descricao'] ?></td>
				<td><?= $value[0]['tipo_exame'] ?></td>
				<td><?= $value[0]['credenciado'] ?></td>
				<td><?= $value[0]['fornecedor_cidade'] ?></td>
				<td><?= $value[0]['fornecedor_estado'] ?></td>
				<td><?= $value[0]['respondido_lyn'] ?></td>
				<td><?= AppModel::dbDateToDate($value[0]['data_emissao']) ?></td>
				<td><?= AppModel::dbDateToDate($value[0]['data_resultado']) ?></td>
				<td><?= AppModel::dbDateToDate($value[0]['data_baixa']) ?></td>
				<td><?= $value[0]['fornecedor_particular'] ?></td>
				<td><?= $value[0]['tipo_resultado'] ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
            <td colspan = "21"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['PedidoExame']['count']; ?></td>
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
<?php endif;?>