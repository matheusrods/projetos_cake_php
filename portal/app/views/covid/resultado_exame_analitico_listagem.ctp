
<table class="table table-striped" style='width:3000px;max-width:none;'>
    <thead>
        <tr>
			<th>Código da Unidade</th>
			<th>Razão Social</th>
			<th>Nome Fantasia</th>			
			<th>Setor</th>
			<th>Cargo</th>
			<th>Funcionário</th>
			<th>CPF</th>
			<th>Matrícula</th>
			<th>Resultado</th>
			<th>Data do Resultado</th>
		</tr>
	</thead>
	<tbody>
		<?php $total = 0 ?>
		<?php foreach($dados as $key => $value) : ?>
			<?php $total ++; ?>
			<tr>
				<td><?= $value[0]['unidade_codigo'] ?></td>
				<td><?= $value[0]['unidade_razao_social'] ?></td>
				<td><?= $value[0]['unidade_nome_fantasia'] ?></td>
				<td><?= $value[0]['setor_descricao'] ?></td>
				<td><?= $value[0]['cargo_descricao'] ?></td>
				<td><?= $value[0]['funcionario'] ?></td>
				<td><?= $value[0]['cpf'] ?></td>
				<td><?= $value[0]['matricula'] ?></td>
				<td><?= $value[0]['resultado_exame'] ?></td>
				<td><?= AppModel::dbDateToDate($value[0]['data_resultado']) ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
            <!-- <td colspan = "10"><strong>Total</strong> <?php //echo $this->Paginator->params['paging']['UsuarioGca']['count']; ?></td> -->
           	<td colspan = "10"><strong>Total</strong> <?php echo $total; ?></td>
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