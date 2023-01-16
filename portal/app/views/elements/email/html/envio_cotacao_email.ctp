<div style="font-family:verdana;">
	<h2>Cotação Online</h2><br>
</div>
<br>
<table width="80%" style="font-family:verdana;">
	<?php if(!empty($cliente)) { ?>
	<tr>
		<td style="font-size:12px;"><strong>Cliente:</strong></td>
		<td style="font-size:12px;"><?php echo $cliente; ?></td>
	</tr>
	<?php } ?>
	<?php if(!empty($email_cliente)) { ?>
	<tr>
		<td style="font-size:12px;"><strong>E-mail:</strong></td>
		<td style="font-size:12px;"><?php echo $email_cliente; ?></td>
	</tr>
	<?php } ?>
	<?php if(!empty($vendedor)) { ?>
	<tr>
		<td style="font-size:12px;"><strong>Vendedor:</strong></td>
		<td style="font-size:12px;"><?php echo $vendedor; ?></td>
	</tr>
	<?php } ?> <?php if(!empty($forma_pagto)) { ?>
	<tr>
		<td style="font-size:12px;"><strong>Forma de recebimento:</strong></td>
		<td style="font-size:12px;"><?php echo $forma_pagto; ?></td>
	</tr>
	<?php } ?>
</table>
<br>
<table width="80%" style="font-family:verdana;font-size:12px;border-collapse:collapse;">
	<thead>
		<tr>
			<th style="border:1px solid;">Serviço</th>
			<th style="text-align:center;border:1px solid;">Quantidade</th>
			<th style="text-align:center;border:1px solid;">Valor Unitário</th>
			<th style="text-align:center;border:1px solid;">Valor total</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($dados['ItemCotacao'] as $key => $cotacao) { ?>
		<tr>
			<td style="border:1px solid;"><?php echo $cotacao['Servico']['descricao'] ?></td>
			<td style="text-align:right;border:1px solid;"><?php echo $cotacao['quantidade'] ?></td>
			<td style="text-align:right;border:1px solid;"><?php echo $this->Buonny->moeda($cotacao['valor_unitario'], array('nozero' => false, 'places' => 2)); ?></td>
			<td style="text-align:right;border:1px solid;"><?php echo $this->Buonny->moeda(($cotacao['valor_unitario'] * $cotacao['quantidade']), array('nozero' => false, 'places' => 2));  ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td style="border:1px solid;" colspan="2">
				<span><strong>Total: </strong><?php echo count($dados['ItemCotacao']) ?></span>  
			</td>
			<td colspan="2" style="text-align:right;border:1px solid;">
				<span style="text-align:right"><strong>Valor total: </strong><?php echo $this->Buonny->moeda($dados['Cotacao']['valor_total'], array('nozero' => false, 'places' => 2)); ?></span>
			</td>
		</tr>
	</tbody>
</table>