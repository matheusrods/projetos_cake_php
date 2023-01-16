<style>
	body {font-family: Arial; color: #000;}
	table, th, td{ border: 1px solid black; font-size: 9pt;}
	table.semborda {border: 0px;}
	table.semborda tr {border: 0px;}
	table.semborda td {border: 0px;}
	.titulo {background-color: #3B90DF; color: #fff; text-align: center; font-size: 9pt; margin: 3px; font-weight: bold; padding: 3px;}
	.corpo {font-size: 9pt; }
</style>
<p class="titulo">
	PROPOSTA COMERCIAL <?=strtoupper($tipo_aprovacao)?> INTERNAMENTE
</p>

<p class="corpo">
Prezados,
</p>

<p class="corpo">
A proposta abaixo foi <?=$tipo_aprovacao?> internamente:<br/>
<table width="100%" class="semborda">
	<tr>
		<td width="120px" align="right"><b>Num. Proposta: </b></td>
		<td align="left"><?=$dados_proposta['Proposta']['numero_proposta']?></td>
	</tr>
	<tr>
		<td width="120px" align="right"><b>Versao: </b></td>
		<td align="left"><?=$dados_proposta['Proposta']['versao']?></td>
	</tr>
	<tr>
		<td width="120px" align="right"><b>Cliente: </b></td>
		<td align="left"><?=$dados_proposta['Proposta']['razao_social']?></td>
	</tr>
	<tr>
		<td width="120px" align="right" valign="top"><b>Produtos: </b></td>
		<td align="left">
			<ul>
			<?php foreach ($produtos as $key => $produto) {
			    echo "<li>".$produto."<br/></li>";
			}
			?>
			</ul>
		</td>
	</tr>
</table>
</p>

<p class="corpo">
Atenciosamente
</p>

<p class="corpo">
Buonny - Depto. Comercial
</p>
