<h1>Exames à vencer:</h1>
<ul>
<?php foreach($ItemPedidoExame as $key => $exame): 
		if (!isset($ult_empresa) || $ult_empresa!=$exame['codigo_cliente']) {
			$ult_empresa = $exame['codigo_cliente'];
			echo "<br><b><li>Empresa: (".$exame['codigo_cliente'].") ".$exame['razao_social']."</li></b><br>";
		} ?>
    <?php echo $exame['codigo_funcionario']." - ".$exame['funcionario']." - <b>".$exame['exame_descricao']."</b><br>"; ?>
<?php endforeach; ?>
</ul>

