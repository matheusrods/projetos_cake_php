<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
            Prezado <?php echo $dados['nome'];?><br/><br/>
            O pedido <strong><?php echo $dados['numero_pedido']; ?></strong>, foi <b style="color: red;">CANCELADO</b> em nosso sistema!<br/><br/><br/>
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
        	<b>Exames Cancelados:</b><br/>
        	================================================================<br/>
        	<?php foreach($dados['itens'] as $codigo_item => $item) : ?>
        		<?php echo $item;?><br/>
        		================================================================<br/>
        	<?php endforeach; ?>
        	
        </td>
    </tr>
    <tr>
        <td style="font-size:12px;">
        	<br/><br/><br/>
        	Att,<br />
        	<b>Equipe RH Health</b><br/>
            Tel. 0800.014.2659<br/>
            <a href="http://<?php echo $_SERVER['SERVER_NAME']; ?>" target="_blank">www.rhhealth.com.br</a><br />
        </td>
    </tr>
</table>