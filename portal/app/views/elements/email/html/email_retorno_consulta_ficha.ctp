<h3 align="center">
	<?= $dado['codigo_produto'];?>
</h3>
<h4><?= $dado['cliente']?></h4>
<hr>
<h4>STATUS</h4>
<p><b><?= iconv('iso-8859-1', 'utf-8', $dado['mensagem']);?></b>
<?php if(!$dado['bloquear_numero_consulta']):?>
	<p><?= $dado['mensagem_cliente']?></p>
<?php else:?>	
<p>An&aacute;lise de perfil para embarcador - Simples confer&ecirc;ncia.</p>
<?php endif;?>
<hr>
<hr>
<table>
	<tr>
		<td width="300"><b>NOME</b></td>
		<td><?=$dado['profissional_nome'];?></td>
	</tr>
	<tr>
		<td width="300"><b>ORIGEM</b></td>
		<td><?= iconv('iso-8859-1', 'utf-8', $dado['origem']);?></td>
	</tr>
	<tr>
		<td width="300"><b>DESTINO</b></td>
		<td><?= iconv('iso-8859-1', 'utf-8',  $dado['destino']);?></td>
	</tr>
	<tr>
		<td width="300"><b>VALOR DA CARGA</b></td>
		<td><?= $dado['valor_carga'];?></td>
	</tr>
	<tr>
		<td><b>RG</b></td>
		<td><?=$dado['profissional_rg'];?></td>
	</tr>
	<tr>
		<td><b>PLACA</b></td>
		<td><?= strtoupper($dado['placa']);?></td>
	</tr>
	<tr>
		<td><b>CARRETA</b></td>
		<td><?= strtoupper($dado['placa_carreta']);?></td>
	</tr>
	<tr>
		<td><b>N&Uacute;MERO DA CONSULTA</b></td>
		<td><?= (($dado['bloquear_numero_consulta']) ? 'XXXXXXXX' : $dado['numero_liberacao']).','.$dado['data_inclusao'];?></td>
	</tr>
	<tr>
		<td><b>VALIDADE</b></td>
		<td>
			<?php if(!$dado['bloquear_numero_consulta']):?>
				<?php if(($dado['codigo'] == '1') || ($dado['codigo'] == '124')):?>
					O Embarque
				<?php else:?>
					XXXXXXXX
				<?php endif?>	
			<?php else:?>
				XXXXXXXX
			<?php endif;?>
		</td>		
	</tr>
</table>
<span style="font-weight:bold;">
	<p><b>ATEN&Ccedil;&Atilde;O : De acordo com o contrato de presta&ccedil;&atilde;o de servi&ccedil;os de Teleconsult &eacute; expressamente 
	proibida a exibi&ccedil;&atilde;o deste documento a consultados ou a terceiros, e a viola&ccedil;&atilde;o desta norma, 
	acarretar&aacute; &agrave; contratante e ao funcion&aacute;rio infrator, responsabilidade civil e criminal.
	A contrata&ccedil;&atilde;o ou n&atilde;o do profissional consultado, &eacute; uma decis&atilde;o da empresa consultante, n&atilde;o 
	cabendo &agrave; Buonny qualquer responsabilidade sobre esta decis&atilde;o.</b></p>
</span>

