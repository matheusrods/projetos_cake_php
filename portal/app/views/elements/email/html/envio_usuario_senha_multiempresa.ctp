<table width="500" style="font-family:verdana;">
	<tr>
		<td style="font-size:12px;" colspan="2">
			Olá <strong><?php echo $dados['Usuario']['nome']; ?></strong>,
			<br /><br />
		</td>
	</tr>
	<tr>
		<td style="font-size:12px;" colspan="2">Ficamos muito felizes com o seu interesse em se tornar um de nossos parceiros. Estamos disponibilizando abaixo os dados de acesso para você experimentar nosso sistema multi empresa.<br /><br /></td>
	</tr>
	<tr>
		<td style="font-size:12px; font-weight: bold;" colspan="2"><br />Anote suas informações e acesse nosso sistema.<br /><br /></td>
	</tr>			
	<tr style="border-top: 2px solid #000;">
		<td style="font-size:12px; border-top: 2px solid #000;"><br /><strong>Usuário:</strong></td>
		<td style="font-size:12px; border-top: 2px solid #000;"><br /><?php echo $dados['Usuario']['apelido']; ?><br /></td>
	</tr>
	<tr>
		<td style="font-size:12px; border-bottom: 2px solid #000;"><br /><strong>Senha:</strong><br /><br /></td>
		<td style="font-size:12px; border-bottom: 2px solid #000;">
			<br /><?php echo $dados['Usuario']['senha']; ?><br /><br />
		</td>
	</tr>
    <tr>
        <td style="font-size:12px;" colspan="2">
        	<br /><br />
        </td>
    </tr>			
	<tr>
		<td style="font-size:12px; border: 2px solid #000; text-align: center;" colspan="2">
			<br />Este é um e-mail somente informativo, caso tenha alguma dúvida ou quiser falar com a gente, entre em contato por: [credenciamento@rhhealth.com.br - 0800 0142659]<br /><br />
		</td>
	</tr>
    <tr>
        <td style="font-size:12px;" colspan="2">
        	<br /><br />
        	Att,<br />
        	<b>Equipe RH Health</b><br />
            <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">www.rhhealth.com.br</a>
        </td>
    </tr>
</table>