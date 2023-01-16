<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;" colspan="2">
        	Olá,
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">
            O contrato do credenciado <?php echo !empty($dados['PropostaCredenciamento']['codigo']) ? $dados['PropostaCredenciamento']['codigo'] : ''?> <?php echo !empty($dados['PropostaCredenciamento']['nome_fantasia']) ? (" - ".$dados['PropostaCredenciamento']['nome_fantasia']) : ''?> está disponível no portal.
        </td>
    </tr>   
    <tr>
        <td style="font-size:12px;" colspan="2">
        	<a href="<?php echo Ambiente::getUrl(); ?>" target="_blank">Acessar o portal agora.</a>
        </td>
    </tr>    
    <tr>
        <td style="font-size:12px;">
        	<br />
        	Att,<br />
        	<b>Equipe RH Health</b><br />
            <a href="<?php echo Ambiente::getUrl(); ?>" target="_blank">www.rhhealth.com.br</a>
        </td>
    </tr>
</table>