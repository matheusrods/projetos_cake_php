<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;" colspan="2">
        	Olá <strong><?php echo (isset($dados['responsavel_administrativo']) && !empty($dados['responsavel_administrativo'])) ? $dados['responsavel_administrativo'] : $dados['nome_fantasia']; ?></strong>,
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">
        	Seja bem-vindo. Estamos felizes em contar com você em nosso time. Esperamos que seja uma ótima experiência.<br /><br />
		</td>
	</tr>
	<tr>
        <td style="font-size:12px;" colspan="2">
        	Sua grade de serviços já foi definida, e você já pode acessar o portal para validar a proposta de credenciamento.<br />
		</td>
	</tr>
	<tr>
        <td style="font-size:12px;" colspan="2">
        	Mais uma vez, seja muito bem-vindo ao time RH Health.<br />
		</td>
	</tr>	
    <tr>
        <td style="font-size:12px;" colspan="2">
        	<br />
        	<a href="<?php echo Ambiente::getUrl(); ?>" target="_blank">Acessar o portal agora!</a><br />
        </td>
    </tr>    
    <tr>
        <td style="font-size:12px;">
        	<br /><br />
        	Att,<br />
        	<b>Equipe RH Health</b><br />
            <a href="<?php echo Ambiente::getUrl(); ?>" target="_blank">www.rhhealth.com.br</a>
        </td>
    </tr>
</table>