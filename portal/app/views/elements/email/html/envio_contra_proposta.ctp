<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
        	Olá <strong><?php echo (isset($dados['PropostaCredenciamento']['responsavel_administrativo']) && !empty($dados['PropostaCredenciamento']['responsavel_administrativo'])) ? $dados['PropostaCredenciamento']['responsavel_administrativo'] : $dados['PropostaCredenciamento']['nome_fantasia']; ?></strong>,
        	<br /><br />
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;">
            Obrigado pela confiança e disponibilidade. Analisamos as informações do seu cadastro e desenvolvemos uma proposta especialmente pensada para você.<br /><br />
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
            Ela já está disponível para sua análise e aprovação, e você pode encontrá-la em nosso portal. <a href="<?php echo Ambiente::getUrl(); ?>" target="_blank">Acessar o Portal.</a><br />
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
            Se tiver alguma dúvida ou quiser falar com a gente, é só entrar em contato por: [credenciamento@rhhealth.com.br - 0800 0142659]<br /><br />
        </td>
    </tr>        
    <tr>
        <td style="font-size:12px;">
        	<br />
        	Att,<br />
        	<b>Equipe RH Health</b><br />
            <a href="<?php echo Ambiente::getUrl(); ?>" target="_blank">www.rhhealth.com.br</a><br />
        </td>
    </tr>
</table>