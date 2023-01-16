<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
        	Olá, <strong><?php echo $dados['nome']; ?></strong>, tudo bem?
        	<br /><br />
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
            Estamos entrando em contato para confirmar o agendamento referente ao pedido do exame <?php echo $dados['pedido_exame']; ?>.<br /><br />
            O material para o atendimento do funcionário <b><?php echo $dados['funcionario_nome']; ?></b>, da empresa <b><?php echo $dados['cliente_nome']; ?></b>, está anexo nesta mensagem.<br /><br />
        
            Em caso de dúvidas, não pense duas vezes antes de entrar em contato conosco. O telefone é o 0800-591-0286.<br /><br />
          Obrigado!<br /><br />
        </td>
    </tr>       
    <tr>
        <td style="font-size:12px;">
            <br />
            Um abraço<br />
            <b>Equipe RH Health</b><br />
            <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">www.rhhealth.com.br</a><br />
        </td>
    </tr>
</table>