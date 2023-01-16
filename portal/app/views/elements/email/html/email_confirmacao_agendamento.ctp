<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
            Confirmação de agendamento de exame(s)<br/><br/>
            Prezado cliente, <?php echo $dados['cliente_nome'];?><br/><br/>
            O(s) exame(s) do funcionário <?php echo $dados['funcionario_nome'];?> está agendado conforme abaixo:<br/><br/>
        </td>
    </tr>
	<?php for($i = 0; $i < (count($dados)-2); $i++){?>
		<tr>
	        <td style="font-size:12px;">
	        	<b>Exame:</b> <?php echo $dados[$i]['exame'];?><br/>
	        	<b>Fornecedor:</b> <?php echo $dados[$i]['empresa_nome'];?><br/>
	        	<b>Endereço Fornecedor:</b> <?php echo $dados[$i]['empresa_endereco'];?><br/>   
	        	<?php if($dados[$i]['data'] == ''){?>
	        		<b>Obs: Exame realizado por Ordem de Chegada.</b><br/><br/><br/>
				<?php }else{ ?>
					<b>Data:</b> <?php echo $dados[$i]['data'];?>
					<b>Horário:</b> <?php echo $dados[$i]['hora'];?>
					<br/><br/>
				<?php }?>	
	        </td>
	    </tr>
    <?php }?>
    <tr>
        <td style="font-size:12px;">
        	Att,<br />
        	<b>Equipe RH Health</b><br/>
            Tel. 0800.014.2659<br/>
            <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">www.rhhealth.com.br</a><br />
        </td>
    </tr>
</table>