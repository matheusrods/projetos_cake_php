<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
        	Olá <strong><?php echo $dados['fornecedor']; ?></strong>,
        	<br /><br />
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;">
            Esta é uma notificação de: <?php echo $dados['tipo_notificacao']; ?><br />
            Do Paciente: <?php echo  $dados['funcionario']; ?><br />
            (Funcionário da Empresa: <?php echo $dados['cliente']; ?>)<br /><br /> 
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
            Confirmando o pedido do exame: <?php echo $dados['exame']; ?>
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;">
        	<br />
        	Att,<br />
        	<b>Equipe RH Health</b><br />
            <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">www.rhhealth.com.br</a><br />
        </td>
    </tr>
</table>