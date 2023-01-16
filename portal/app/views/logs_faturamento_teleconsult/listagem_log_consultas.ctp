<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table class='table table-striped horizontal-scroll' > <!--style='width:2000px;max-width:none;' -->
	<thead>
		<th>Código</th>
        <th>Razão Social</th>
		<th>Usuário</th>
		<th>Cadastro</th>
		<th>Operação</th>
		<th>Categoria</th>
		<th>CPF</th>
		<th>Data</th>
		<th>CPF</th>
		<th>Data</th>
		<th>Nº Consulta</th>
		<th>Placa</th>
		<th>Carreta</th>
		<th>Bitrem</th>
		<th>Origem</th>
		<th>Destino</th>
		<th>Carga</th> 
	</thead>
	<tbody>		
	<?php $id_label=0; 
    debug($fichasScorecard); 
	foreach ($fichasScorecard as $ficha):?>		
		<?php $tempo_atendimento = Comum::diffDuracao(strtotime(AppModel::dateToDbDate($ficha['FichaScorecard']['data_inclusao'])), strtotime($ficha['0']['data_pesquisa']), array('minutos')); ?>
		<tr>
			<td><?php echo $ficha[0]['codigo_cliente']?></td>
			<td><?php echo $ficha[0]['nome_fantasia']?></td>
			<td><?php echo $ficha[0]['apelido'] ?></td>
			<td><?php echo $ficha['ProfissionalLog']['codigo_documento']; ?></td>
			<td><?php echo $ficha['ProfissionalTipo']['descricao']; ?></td>
			<td><?php echo $ficha['ProprietarioLog']['nome_razao_social'];?></td>
			<td><?php echo $ficha['ProprietarioLog']['codigo_documento'];?></td>
			<td><?php echo $ficha['FichaScorecard']['data_inclusao'];?></td>
			<td><?php echo AppModel::dbDateToDate($ficha['0']['data_pesquisa']);?></td>
			<td><?php echo AppModel::dbDateToDate($ficha['FichaScorecard']['data_validade']);?></td>
			<td><?php echo strtoupper($ficha['VeiculoLog']['placa']);?></td>
			<td class='numeric'><?php echo $tempo_atendimento['minutos'] ?></td>
			<td><?php echo $ficha['0']['apelido'];?></td>
			<td><?php echo $ficha['0']['usuario_alteracao'];?></td>
			<td><?php echo $status[$ficha['FichaScorecard']['codigo_status']]; ?></td>
			<td><?php echo ParametroScore::formataResultadoPorTipoProfissional($ficha['FichaScorecard']['codigo_profissional_tipo'], $ficha['ParametroScore']['pontos'], $ficha['ParametroScore']['nivel']); ?></td>
			<td><?php echo $this->Buonny->moeda($ficha['ParametroScore']['valor'], array('nozero' => true)); ?></td>
			<td><?php echo $ficha['Servico']['descricao']; ?></td>
		</tr>
	<?php endforeach ?>
	</tbody>
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
        <?php if($this->Paginator->counter('{:pages}') > 1): ?>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        <?php endif; ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>