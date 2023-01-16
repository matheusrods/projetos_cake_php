<table class='table table-striped embarcador-transportador tablesorter'>
	<thead>
		<tr>
			<th class='input-xlarge'><?= $this->Html->link('Transportador', 'javascript:void(0)') ?></th>
			<th class='input-xlarge'><?= $this->Html->link('Embarcador', 'javascript:void(0)') ?></th>
			<th class='input-small'><?= $this->Html->link('Tipo Veiculo', 'javascript:void(0)') ?></th>
			<th class='input-small numeric'><?= $this->Html->link('Qtd Viagens', 'javascript:void(0)') ?></th>
			<th class='input-medium numeric'><?= $this->Html->link('Maior Valor', 'javascript:void(0)') ?></th>
			<th class='input-medium numeric'><?= $this->Html->link('Total Transportado', 'javascript:void(0)') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $total = 0 ?>
		<?php $total_valor = 0 ?>
		<?php $total_valor_total = 0 ?>
		<?php foreach ($historicoEmbarcadorTransportador as $row): ?>
			<?php $total += $row[0]['total'] ?>
			<?php $total_valor += $row[0]['max_valor'] ?>
			<?php $total_valor_total += $row[0]['valor_total'] ?>
			<tr>
				<td><?php echo $row['EmpresaCliente']['Raz_Social']//$row['Embarcador']['pess_nome'] ?></td>
				<td><?php echo $row['EmpresaRelacionada']['Raz_Social']//$row['Transportador']['pess_nome'] ?></td>
				<td><?php echo $row['MMonTipocavalocarreta']['TIP_Descricao']//$row['TTveiTipoVeiculo']['tvei_descricao'] ?></td>
				<td class='numeric' ><?php echo $row[0]['total'] ?></td>
				<td class='numeric' ><?php echo $this->Buonny->moeda($row[0]['max_valor']) ?></td>
				<td class='numeric' ><?php echo $this->Buonny->moeda($row[0]['valor_total']) ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
	<?php if($total || $total_valor): ?>
		<tfoot>		
				<tr>
					<td>Total</td>
					<td></td>
					<td></td>
					<td class='numeric' ><?php echo $total ?></td>
					<td class='numeric' ><?php echo $this->Buonny->moeda($total_valor) ?></td>
					<td class='numeric' ><?php echo $this->Buonny->moeda($total_valor_total) ?></td>
				</tr>
		</tfoot>
	<?php endif; ?>
</table>

<?php echo $this->Javascript->codeBlock("jQuery('table').tablesorter()") ?>