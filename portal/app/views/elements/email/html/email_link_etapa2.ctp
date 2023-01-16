<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;" colspan="2">
        	Olá <strong><?php echo (isset($dados['responsavel_administrativo']) && !empty($dados['responsavel_administrativo'])) ? $dados['responsavel_administrativo'] : $dados['nome_fantasia']; ?></strong>. Tudo bem?
        	<br /><br />
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">Confiamos que, para uma empresa realizar um bom trabalho, todos os seus colaboradores precisam estar em dia com sua saúde, segurança e qualidade de vida.<br /><br /></td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">Pensando nisso, estamos reunindo um time de profissionais especializados e com boa experiência na área da saúde. Acreditamos que você pode ser esse profissional e que, juntos, podemos oferecer aos nossos clientes uma gestão integrada da saúde corporativa.<br /><br /></td>
    </tr>
    <tr>
        <td style="font-size:12px;" colspan="2">
        	===========================================================<br />
        	<a href="<?php echo Ambiente::getUrl(); ?>/portal/propostas_credenciamento/etapa2/<?php echo base64_encode($codigo); ?>" target="_blank">CLIQUE AQUI PARA EFETUAR/CONCLUIR SEU CADASTRO.</a><br />
        	===========================================================<br /><br />
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;">
        	<br /><br />
        	Att,<br />
        	<b>Equipe RH Health</b><br />
            <a href="<?php Ambiente::getUrl(); ?>" target="_blank">www.rhhealth.com.br</a>
        </td>
    </tr>   
</table>