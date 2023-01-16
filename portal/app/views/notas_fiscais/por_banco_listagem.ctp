<?php if(!empty($notas_fiscais_por_banco)): ?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th title="Banco">Banco</th>
				<th class="numeric input-large" title="Número de Notas Fiscais">Número de Notas Fiscais</th>
			</tr>
		</thead>
		<tbody>
			<?php $total_de_notas = 0; ?>
			<?php foreach($notas_fiscais_por_banco as $nota_fiscal_por_banco): ?>
			<tr>
				<td><?php echo $nota_fiscal_por_banco[0]['numero_banco']." - ".$nota_fiscal_por_banco[0]['nome_banco']; ?></td>
				<td class="numeric">
					<?php
						if($nota_fiscal_por_banco[0]['numero_de_notas'] != 0){
							echo $nota_fiscal_por_banco[0]['numero_de_notas'];
							$total_de_notas = $total_de_notas + $nota_fiscal_por_banco[0]['numero_de_notas'];
						}
					?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<th>TOTAL</th>
				<th class="numeric"><?php echo $total_de_notas; ?></th>
			</tr>
		</tfoot>
	</table>
<?php endif; ?>