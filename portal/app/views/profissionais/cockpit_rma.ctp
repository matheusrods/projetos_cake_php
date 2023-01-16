<table class='table table-striped sinistro tablesorter'>
	<thead>
		<th><?= $this->Html->link('Tipo RMA', 'javascript:void(0)') ?></th>
		<th class='input-small numeric '><?= $this->Html->link('Qtd', 'javascript:void(0)') ?></th>
	</thead>
	<tbody>
		<?php $qtd_total = 0; ?>
		<?php foreach ($historicoRMA as $row): ?>
			<tr>
				<td><?php echo $row[0]['titulo_ocorrencia'] ?></td>
				<td class="numeric" ><?php echo $row[0]['qtd'] ?></td>
			</tr>
			<?php $qtd_total += $row[0]['qtd']; ?>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<td>Total</td>
			<td class='numeric'><?php echo $qtd_total ?></td>
		</tr>
	</tfoot>
</table>

<?php echo $this->Javascript->codeBlock("jQuery('table.sinistro').tablesorter()")?>