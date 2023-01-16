<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
        	Olá <strong><?php echo $dados['nome']; ?></strong>,
        	<br /><br />
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;">
            Esta é uma notificação de: <?php echo $dados['tipo_notificacao']; ?><br /><br />
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
            Confirmando o pedido do exame: <?php echo $dados['pedido_exame']; ?>.<br /><br />
            O(s) relatório(s) de agendamento do(s) exame(s) segue(m) em anexo.
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