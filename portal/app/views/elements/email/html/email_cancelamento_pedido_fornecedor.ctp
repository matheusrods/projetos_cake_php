<table width="500" style="font-family:verdana;">
    <tr>
        <td style="font-size:12px;">
            Prezado <?php echo $dados['nome'];?><br/><br/>
            O pedido <?php echo $dados['numero_pedido']; ?>, foi <b style="color: red;">CANCELADO</b> em nosso sistema!<br/><br/>
            
            Paciente: <?php echo $dados['funcionario']; ?><br/>
            Cliente: <?php echo $dados['cliente']; ?><br/><br/><br/>
        </td>
    </tr>
	<tr>
        <td style="font-size:12px;">
        	<b>Exames Cancelados:</b><br />
        	================================================================<br/>
        	<?php foreach($dados['itens'] as $codigo_item => $item) : ?>
        		<?php echo $item['exame'];?>
        		<?php if($item['data_agendamento']) : ?>
        			<strong style="color:red; font-size: 11px;">- CANCELADO AGENDAMENTO (<?php echo $item['data_agendamento']; ?>)</strong>
        		<?php endif; ?><br />
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