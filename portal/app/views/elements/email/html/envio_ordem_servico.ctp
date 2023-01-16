<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">Olá <?php echo $dados_fornecedor['Fornecedor']['nome']; ?>,</td>
    </tr>
    <tr>
        <td style="font-size:12px;">Você recebeu uma ordem de serviço para prestação de serviço de PGR.</td>
    </tr>    
    <tr>
        <td style="font-size:14px;">
            <b>Ordem de Serviço Número:</b> #<?php echo $id_ordem; ?><br /><br />
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
        	===================================================================<br />
            Dados Cliente:<br />
            ===================================================================<br /><br />
            Razão Social: <?php echo $dados_cliente['Cliente']['razao_social']; ?><br />
            Nome fantasia: <?php echo $dados_cliente['Cliente']['nome_fantasia']; ?><br />
            <?php if($dados_cliente['ClienteContato']['ddd'] && $dados_cliente['ClienteContato']['descricao']) : ?>
            	<?php echo $dados_cliente['ClienteContato']['ddd'] . " - " . $dados_cliente['ClienteContato']['descricao']; ?><br />
            <?php endif; ?>
            <?php echo $dados_cliente['ClienteEndereco']['logradouro']; ?>, <?php echo $dados_cliente['ClienteEndereco']['numero']; ?> - <?php echo $dados_cliente['ClienteEndereco']['bairro']; ?> <br />
            <?php echo $dados_cliente['ClienteEndereco']['cidade']; ?> / <?php echo $dados_cliente['ClienteEndereco']['estado_descricao']; ?>
        </td>
    </tr>
    
    <?php if(isset($servico) && $servico != '') : ?>
	    <tr>
	        <td style="font-size:12px;">
	        	===================================================================<br />
	            Serviço Solicitado:<br />
	            ===================================================================<br /><br />
	            <?php echo $servico; ?>
	        </td>
	    </tr>    
    <?php endif; ?>
    
    <tr>
        <td style="font-size:12px;">
        	<br />
        	<a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">Acessar o Portal</a>
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