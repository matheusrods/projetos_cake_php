<table class='table table-striped sinistro tablesorter'>
	<thead>
		<th><?= $this->Html->link('Natureza', 'javascript:void(0)') ?></th>
		<th class='input-small numeric '><?= $this->Html->link('Qtd Eventos', 'javascript:void(0)') ?></th>
		<th class='input-xlarge '><?= $this->Html->link('Data Último Sinístro', 'javascript:void(0)') ?></th>
		
	</thead>
	<tbody>
		<?php $total = 0 ?>
		<?php foreach ($historicoSinistro as $row): ?>
			<?php $total += $row[0]['total'] ?>
			<tr>
				<td><?php echo $natureza[$row['Sinistro']['natureza']] ?></td>
				<td class="numeric" ><?php echo $row[0]['total'] ?></td>
				<td><?php echo $row[0]['ultima_data'] ?></td>
				
			</tr>
		<?php endforeach ?>
	</tbody>
	<?php if($total): ?>
		<tfoot>		
				<tr>
					<td>Total</td>
					<td class='numeric' ><?php echo $total ?></td>
					<td></td>
				</tr>
		</tfoot>
	<?php endif; ?>
</table>

<?php echo $this->Javascript->codeBlock("jQuery('table.sinistro').tablesorter()")?>