<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;" colspan="2">Olá <strong><?php echo $dados['PropostaSemValidacao']['nome_fantasia']; ?></strong>,</td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">
        	Temos uma proposta para você e gostariamos que tê-los como nosso parceiro.<br />
        	Por Favor acesse nosso site clicando aqui: <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/portal/propostas_credenciamento/etapa2/<?php echo base64_encode($codigo); ?>" target="_blank">Clicando Aqui</a>
			<br /><br /><br />
		</td>        
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">
        	<br />
        	<a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">ACESSAR O PORTAL AGORA</a>
        </td>
    </tr>    
    <tr>
        <td style="font-size:12px;">
        	<br /><br />
        	Att,<br />
        	<b>Equipe RH Health</b><br />
            <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">www.rhhealth.com.br</a>
        </td>
    </tr>   
</table>