<?php 
	if ($tipo_empresa == 4):
		$empty = 'Transportadora';
	elseif ($tipo_empresa == 1):
		$empty = 'Embarcador';
	else:
		$empty = 'Selecione um cliente';
	endif;
?>
<option value=''><?php echo $empty ?></option>
<?php foreach ($clientes_monitora as $key => $razao_social): ?>
	<option value='<?php echo $key ?>'><?php echo $razao_social ?></option>
<?php endforeach ?>