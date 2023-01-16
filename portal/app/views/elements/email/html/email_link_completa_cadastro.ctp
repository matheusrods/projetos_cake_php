<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;" colspan="2"><strong><?php echo $dados['nome_fantasia']; ?></strong>,</td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">Acreditamos que, para uma empresa realizar um bom trabalho, todos os seus funcionários necessitam de plena saúde, segurança e qualidade de vida.<br /></td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">Pensando nisso, reunimos um time de profissionais especializados com comprovada experiência no mercado de saúde, para oferecer aos nossos clientes uma gestão integrada de saúde corporativa.<br /><br /></td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">Faça parte também do nosso time, seja um de nossos credenciados!<br /><br /></td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2"><strong>Usuário:</strong> <?php echo $dados['Usuario']['apelido']; ?></td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2"><strong>Senha: <?php echo $dados['Usuario']['senha']; ?></strong></td>
    </tr>    
    <tr>
        <td style="font-size:12px;" colspan="2">
        	===========================================================<br />
        	<a href="<?php echo Ambiente::getUrl(); ?>/portal/propostas_credenciamento/etapa2/<?php echo base64_encode($codigo); ?>" target="_blank">Clique aqui e conclua seu cadastro.</a><br />
        	===========================================================<br /><br />
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