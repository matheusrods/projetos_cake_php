<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
        	Olá credenciado <?php echo $dados['fornecedor']; ?>, tudo bem?
        	<br /><br />
            
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
        Você possui documentos que foram recusados pela nossa equipe.<br><br>
        
        Código do pedido: <b><?php echo $dados['codigo_pedido']; ?></b><br>
        Campo: <b><?php echo $dados['tipo']; ?></b><br>
        Exame: <b><?php echo $dados['exame']; ?></b><br>
        Funcionário: <b><?php echo $dados['nome_funcionario']; ?></b><br><br><br>
        Segue o motivo da recusa:<br><br>
        <b><?php echo $dados['motivo_recusa']; ?></b><br /><br />

        Aguardamos o envio dos documentos corretos.<br /><br />
        Muito obrigado pela atenção e tenha um bom dia!<br /><br />
        </td>
    </tr>       
    <tr>
        <td style="font-size:12px;">
            <br />
            Um abraço<br />
            <b>Equipe RH Health</b><br />
            0800-591-0286<br>
            <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">www.rhhealth.com.br</a>
        </td>
    </tr>
</table>