
<?php 
//verifica se existe dados na consulta realizada
if(!empty($dados_exames)):
	echo $paginator->options(array('update' => 'div.lista')); 
?>
	<div class='well'>
		<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
	</div>
	<table class="table table-striped" style='width:3000px;max-width:none;'>
		<thead>
			<tr>
				<th>Código Cliente</th>
				<th>Cnpj</th>
				<th>Razão Social</th>
				<th>Cpf do Funcionário</th>
				<th>Funcionário</th>
				<th>Pedido de Exame</th>
				<th>Exame</th>
				<th>Data do Pedido</th>
				<th>Data Realização</th>
				<th>Data Baixa</th>
				<th>Valor Venda</th>
				<th>Código Credenciado</th>
				<th>Nome Credenciado</th>
				<th>Endereço Credenciado</th>
				<th>Valor Compra</th>
			</tr>
		</thead>
		<tbody>
			<?php $total = 0 ?>
			<?php foreach($dados_exames as $key => $value) : ?>
				<?php $total += 1 ?>
				<tr>
					<td><?php echo $value[0]['codigo_cliente']; ?></td>
					<td><?php echo comum::formatarDocumento($value[0]['cnpj']); ?></td>
					<td><?php echo $value[0]['cli_razao_social']; ?></td>
					<td><?php echo AppModel::formataCpf($value[0]['cpf']); ?></td>
					<td><?php echo $value[0]['nome_funcionario']; ?></td>
					<td><?php echo $value[0]['codigo_pedido_exame']; ?></td>
					<td><?php echo $value[0]['exame']; ?></td>					
					<td><?php echo AppModel::dbDateToDate($value[0]['pedido_exame_data_inclusao']); ?></td>
					<td><?php echo AppModel::dbDateToDate($value[0]['ipe_data_realizacao_exame']); ?></td>
					<td><?php echo AppModel::dbDateToDate($value[0]['data_baixa']); ?></td>
					<td><?php echo $this->Buonny->moeda($value[0]['valor_venda'],array()); ?></td>
					<td><?php echo $value[0]['codigo_credenciado']; ?></td>
					<td><?php echo $value[0]['nome_credenciado']; ?></td>
					<td>
						<?php 
							$complemento = '';
							$dado_complemento = trim($value[0]['complemento_credenciado']);
				        	if(!empty($dado_complemento)) {
				        		$complemento = ' - '.$dado_complemento;
				        	}
				        	$endereco = $value[0]['logradouro_credenciado'].", ".$value[0]['numero_credenciado'].$complemento.", ".$value[0]['bairro_credenciado'].', '.$value['0']['cidade_credenciado'].' - '.$value[0]['estado_credenciado'];

							echo $endereco; 
						?>						
					</td>
					<td><?php echo $this->Buonny->moeda($value[0]['valor_compra'],array()); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<!--td><?= $total ?></td-->
				<td><?= $this->Paginator->counter(array('format' => '%count%')) ?></td>
				<td colspan="14"></td>
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
<?php endif;?>