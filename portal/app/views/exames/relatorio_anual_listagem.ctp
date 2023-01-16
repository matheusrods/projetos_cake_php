<?php if (!empty($error)): ?>
	<div class="alert alert-danger">
		<?php echo $error[0];?>
	</div>
<?php endif; ?>

<?php if (empty($dados['Filtros']['codigo_unidade'])): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php else: ?>
	
	<div class='well'>
		<?php 
		//variaveis para ficar facil o desenvolvimento e manutencao
		$codigo_cliente = $dados['Filtros']['codigo_cliente'];
		$tipo_agrupamento = $dados['Filtros']['tipo_agrupamento'];
		$data_inicio = $dados['Filtros']['data_inicio'];
		$data_fim = $dados['Filtros']['data_fim'];
		$codigo_exame = ((!empty($dados['Filtros']['codigo_exame']))?$dados['Filtros']['codigo_exame']:'null');
		$tipo_exame = ((!empty($dados['Filtros']['tipo_exame']))?$dados['Filtros']['tipo_exame']:'null');
		$codigo_unidade = ((!empty($dados['Filtros']['codigo_unidade']))?$dados['Filtros']['codigo_unidade']:'null');
		$codigo_setor = ((!empty($dados['Filtros']['codigo_setor']))?$dados['Filtros']['codigo_setor']:'null');

		echo $this->Html->link('', array('action' => 'imprimir_relatorio', $codigo_cliente, $tipo_agrupamento, $data_inicio, $data_fim,$codigo_exame,$tipo_exame,$codigo_unidade,$codigo_setor), array('data-toggle' => 'tooltip', 'title' => 'Imprimir relatório', 'class' => 'icon-print ')); 
		?>
	</div>
	<table class="table table-striped" style='width:3000px;max-width:none;'>
		<thead>
			<tr>
				<th>Unidade</th>
				<th>Setor</th>
				<th>Exame</th>
				<th>Números de exames realizados</th>
				<th>Números de resultados normais</th>
				<th>Números de resultados anormais</th>
				<th>(Número de resultados anormais / Número de exames realizados ) x 100</th>
				<th>Número de Exames para o ano seguinte</th>
			</tr>
		</thead>
		<tbody>
			<?php $total = 0 ?>
			<?php foreach($dados as $key => $value) : ?>
				
				<?php if(isset($value[0])): ?>
					<?php $total += 1 ?>
					<tr>
						<td><?php echo $value[0]['descricao']; ?></td>
						<td><?php echo $value[0]['setor']; ?></td>
						<td><?php echo $value[0]['tipo']; ?></td>
						<td><?php echo $value[0]['quantidade']; ?></td>
						<td><?php echo $value[0]['normal']; ?></td>
						<td><?php echo $value[0]['anormal']; ?></td>
						<td><?php echo $value[0]['percentual']; ?>%</td>
						<td><?php echo $value[0]['total_preditivo']; ?></td>
					</tr>
				<?php endif;?>
			<?php endforeach; ?>
		</tbody>
	</table>	
	
<?php endif ?>

