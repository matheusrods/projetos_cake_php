<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;" colspan="2">
        	Olá <strong><?php echo (isset($dados['PropostaCredenciamento']['responsavel_administrativo']) && !empty($dados['PropostaCredenciamento']['responsavel_administrativo'])) ? $dados['PropostaCredenciamento']['responsavel_administrativo'] : $dados['PropostaCredenciamento']['nome_fantasia']; ?></strong>,
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">
        	Sua proposta acaba de ser aprovada.<br /><br />
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">
        	A partir de agora, solicitamos que acesse nosso portal para completar a etapa de envios dos documentos obrigatórios. Após o envio, finalizamos o credenciamento.<br /><br />
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">
        	Se tiver alguma dúvida ou quiser falar com a gente, é só entrar em contato: [credenciamento@rhhealth.com.br - 0800 0142659]<br /><br />
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">
        	<br />	
        	<a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">Acessar o portal agora.</a>
        </td>
    </tr>    
    <tr>
        <td style="font-size:12px;">
        	<br />
        	Att,<br />
        	<b>Equipe RH Health</b><br />
            <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">www.rhhealth.com.br</a>
        </td>
    </tr>
</table>