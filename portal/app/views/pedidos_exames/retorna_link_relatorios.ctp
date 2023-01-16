<?php foreach($dados_relatorio as $k_rel => $relatorio) { ?>

	<?php $relatorios = array(1,4,5); ?>
	<?php if(isset($exibe_audiometria) && $exibe_audiometria) : ?>
		<?php array_push($relatorios, '6'); ?>
	<?php endif; ?>
	
	<?php if(in_array($k_rel, $relatorios)) { ?>
	<h4><?php echo $list_tipos[$k_rel]; ?></h4>
	<table class="table table-striped">
		<tr>
			<td><b>Fornecedor</b></td>
			<td><b>Ação</b></td>
		</tr>
		<?php foreach($relatorio as $key => $linha) { ?>
			<tr>
				<td><?php echo $linha['NOME_FORNECEDOR']; ?></td>
				<td>
					<a href="/portal/pedidos_exames/imprime/<?php echo $codigo_pedido; ?>/<?php echo $linha['CODIGO_FORNECEDOR']; ?>/<?php echo $linha['CODIGO_CLIENTE_FUNCIONARIO']; ?>/<?php echo $k_rel; ?>/<?php echo $codigo_func_setor_cargo?>/<?php echo str_replace(" ", "_", $list_tipos[$k_rel]); ?>" target="_blank">
						<i class="icon-print"></i>
					</a>
				</td>
			</tr>		
		<?php } ?>
	</table>
	<hr />
<?php } ?>
<?php } ?>

<h4>Outros relatórios</h4>
<table class="table table-striped">
		<tr>
			<td><b>Relatório</b></td>
			<td><b>Ação</b></td>
		</tr>
<?php foreach($dados_relatorio as $k_rel => $relatorio) { ?>
	<?php if(in_array($k_rel, array(2, 3, 5, 7, 8))) { ?>
			<tr>
				<td>Relatório de <?php echo $list_tipos[$k_rel]; ?></td>
				<td style="width: 35px;">
					<a href="/portal/pedidos_exames/imprime/<?php echo $codigo_pedido; ?>/null/<?php echo $codigo_cliente_funcionario; ?>/<?php echo $k_rel; ?>/<?php echo $codigo_func_setor_cargo?>/<?php echo str_replace(" ", "_", $list_tipos[$k_rel]); ?>" target="_blank">
						<i class="icon-print"></i>
					</a>
				</td>
			</tr>		
<?php } ?>
<?php } ?>
	</table>