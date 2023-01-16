<?php 

if(!empty($dados)):

    echo $paginator->options(array('update' => 'div.lista')); 
?>
	<div class='well'>
		<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
	</div>
	<table class="table table-striped" style='width:3000px;max-width:none;'>
		<thead>
			<tr>
				<th>Unidade</th>
				<th>Setor</th>
				<th>Cargo</th>
				<th>CPF</th>
				<th>Funcionário</th>
				<th>Código Matrícula</th>
				<th>Matrícula</th>
				<th>Admissão</th>
				<th>Situação</th>
				<th>Tipo Exame</th>
				<th>Exame</th>
				<th>Periodicidade</th>
				<th>Status</th>
				<th>Último Pedido</th>
				<th>Comparecimento</th>
				<th>Data Resultado</th>
				<th>Vencimento</th>
			</tr>
		</thead>
		<tbody>
			<?php $total = 0 ?>
			<?php foreach($dados as $key => $value) : ?>
				<?php $total += 1 ?>
				<tr>
					<td><?=$this->Buonny->leiaMais($value['0']['unidade_descricao'],50)?></td>
					<td><?=$value['0']['setor_descricao']?></td>
					<td><?=$value['0']['cargo']?></td>
					<td><?=AppModel::formataCpf($value['0']['cpf'])?></td>
					<td><?=$value['0']['nome']?></td>
					<td><?=$value['0']['codigo_cf']?></td>
					<td><?=$value['0']['matricula']?></td>
					<td><?=AppModel::dbDateToDate($value['0']['admissao'])?></td>
					<td><?php 
							if($value['0']['situacao'] == 0){
								echo "Inativo";
							}elseif($value['0']['situacao'] == 2){
								echo "Férias";
							}elseif($value['0']['situacao'] == 3){
								echo "Afastado";
							}else{
								echo "Ativo";
							}?>
					</td>
					<td>
						<?php
							$tipo_exame_descricao = $value['0']['tipo_exame_descricao'];
							if($value['0']['tipo_exame_descricao_monitorac'] == "MT") {
								$tipo_exame_descricao = "Monitoramento";
							}
							echo $tipo_exame_descricao;
						?>
					</td>
					<td><?=$value['0']['exame_descricao']?></td>
					<td><?=$value['0']['periodicidade']?></td>
					<td><?php 
							if($value['0']['pendente'] == 1){
								echo "Pendente";
							}elseif($value['0']['vencido'] == 1){
								echo "Vencido";
							}elseif($value['0']['vencer'] == 1){
								echo "À vencer";
							}?>
					</td>
					<td><?=AppModel::dbDateToDate($value['0']['ultimo_pedido'])?></td>
					<td><?=$value[0]['compareceu']?></td>
					<td><?=AppModel::dbDateToDate($value['0']['data_realizacao_exame'])?></td>
					<td><?=AppModel::dbDateToDate($value['0']['vencimento'])?></td>

				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<!--td><?= $total ?></td-->
				<td><?= $this->Paginator->counter(array('format' => '%count%')) ?></td>
				<td colspan="12"></td>
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
	<?php echo $this->Js->writeBuffer(); ?>
<?php else: ?>
	<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>