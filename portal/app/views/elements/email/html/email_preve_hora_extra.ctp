<title>Alerta de provavel Hora Extra n&atilde;o autorizada</title>
<table width=80%>
	<tr>
		<th align="left">Usuario</th>
		<th align="left">Data atual</th>
		<th align="left">Hora Fim do Expediente</th>
	</tr>
	<tr>        
		<td><?=$config_horario_trabalho['Usuario']['apelido']?></td>
		<td><?=date('d/m/Y H:i:s');?></td>
		<td>			
		<?php 
			if( $config_horario_trabalho['Usuario']['escala'] ):				
				echo substr($config_horario_trabalho['UsuarioEscala']['data_saida'], 0, 16);
			else:
				echo date('d/m/Y').' '.trim(substr($config_horario_trabalho['UsuarioExpediente']['saida'], 0, 5));					
			endif;
		?>
		</td>
	</tr>
</table>
<br />