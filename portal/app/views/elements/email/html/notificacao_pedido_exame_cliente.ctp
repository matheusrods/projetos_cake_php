<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
        	Olá <strong><?php echo $dados['cliente']; ?></strong>,
        	<br /><br />
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;">
            Esta é uma notificação de: <?php echo $dados['tipo_notificacao']; ?><br />
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
            Confirmando o pedido do exame: <?php echo $dados['exame']; ?><br /><br />
            Do funcionário: <?php echo $dados['funcionario']; ?><br />
            
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
            Fornecedor: <?php echo $dados['fornecedor']['razao_social']; ?><br />
            Endereço: <?php echo $dados['fornecedor']['endereco']; ?>
            
            <?php if($dados['fornecedor']['endereco']) : ?>
				(distancia de seu endereço: <?php echo $dados['fornecedor']['km']; ?>)            
            <?php endif; ?>
            <br /><br /><br />
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