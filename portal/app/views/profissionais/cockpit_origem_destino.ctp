
<table class='table table-striped origem-detino tablesorter'>	
	<thead>
		<th><?= $this->Html->link('Origem', 'javascript:void(0)') ?></th>
		<th><?= $this->Html->link('Destino', 'javascript:void(0)') ?></th>
		<th class='input-small numeric'><?= $this->Html->link('Qtd', 'javascript:void(0)') ?></th>
	</thead>
	<tbody>
		<?php $total = 0 ?>
		<?php foreach ($historicoOrigemDestino as $row): ?>
			<?php //if($row['OrigemCidade']['cida_descricao'] || $row['DestinoCidade']['cida_descricao']): ?>
			<?php if($row['CidadeOrigem']['Descricao'] || $row['CidadeDestino']['Descricao']): ?>
				<?php $total += $row[0]['total'] ?>
				<tr>
					<td><?php echo "{$row['CidadeOrigem']['Descricao']} / {$row['CidadeOrigem']['Estado']}"//"{$row['OrigemCidade']['cida_descricao']} / {$row['OrigemEstado']['esta_sigla']}" ?></td>
					<td><?php echo "{$row['CidadeDestino']['Descricao']} / {$row['CidadeDestino']['Estado']}"//"{$row['DestinoCidade']['cida_descricao']} / {$row['DestinoEstado']['esta_sigla']}" ?></td>
					<td class="numeric" ><?php echo $row[0]['total'] ?></td>
				</tr>
			<?php endif; ?>
		<?php endforeach ?>
	</tbody>
	<?php if($total): ?>
		<tfoot>		
				<tr>
					<td>Total</td>
					<td></td>
					<td class='numeric' ><?php echo $total ?></td>
				</tr>
		</tfoot>
	<?php endif; ?>

</table>

<?php echo $this->Javascript->codeBlock("jQuery('table.origem-detino').tablesorter()")?>