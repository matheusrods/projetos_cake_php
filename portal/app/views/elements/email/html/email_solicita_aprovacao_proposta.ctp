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
	SOLICITA&Ccedil;&Atilde;O DE APROVA&Ccedil;&Atilde;O DE PROPOSTA COMERCIAL
</p>

<p class="corpo">
Prezados,
</p>

<p class="corpo">
A proposta abaixo foi criada, e possui descontos acima do permitido, necessitando de aprova&ccedil;&atilde;o para envio ao cliente:<br/>
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
		<td width="120px" align="right"><b>Gestor: </b></td>
		<td align="left"><?=$dados_proposta['Gestor']['nome']?></td>
	</tr>
	<tr>
		<td width="120px" align="right"><b>Cliente: </b></td>
		<td align="left"><?=$dados_proposta['Proposta']['razao_social']?></td>
	</tr>
	<tr>
		<td width="120px" align="right" valign="top"><b>Produtos / Servi&ccedil;os: </b></td>
		<td align="left">
			<ul>
			<?php foreach ($produtos as $codigo_produto => $produto) {
			    echo "<li>".$produto.":<hr><br/></li>";
			    echo "<table border=1><tr>";
			    echo "<th class='titulo'>Servi&ccedil;o</th>";
			    echo "<th class='titulo'>Desconto</th>";
			    echo "<th class='titulo'>Valor</th>";
			    echo "</tr>";
			    foreach ($servicos[$codigo_produto] as $key => $servico) {
			    	echo "<tr>";
			    	echo "<td class='corpo'>".iconv('ISO-8859-1','UTF-8',$servico['servico'])."</td>";
			    	echo "<td class='corpo' align='right'>".$servico['perc_desconto']."%</td>";
			    	echo "<td class='corpo' align='right'>".$servico['valor_final']."</td>";
			    }
			    echo "</table>";
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
