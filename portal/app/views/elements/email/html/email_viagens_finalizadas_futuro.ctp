<style>
	table, th, td{ border: 1px solid black; }
</style>
<h3>
	<?php if(date("H") < 12): ?>
		Bom dia!
	<?php elseif(date("H") < 18): ?>
		Boa tarde!
	<?php else: ?>
		Boa noite!
	<?php endif; ?>
</h3>
<p>SM's com data final ou data inicial no futuro: </p>
<?php if(isset($dados) && !empty($dados)): ?>
<table border="3">
	<tr>
		<th>SM</th>
		<th>Data Inicio</th>
		<th>Data Final</th>
		<th>Transportador</th>
		<th>Embarcador</th>
	</tr>
	<?php foreach ($dados as $dado){ ?>
	<tr>
		<th><?php echo $dado['TViagViagem']['viag_codigo_sm'] ?></th>
		<th><?php echo $dado['TViagViagem']['viag_data_inicio'] ?></th>
		<th><?php echo $dado['TViagViagem']['viag_data_fim'] ?></th>
		<th><?php echo $dado['Transportador']['pjur_razao_social'] ?></th>
		<th><?php echo $dado['Embarcador']['pjur_razao_social'] ?></th>
	</tr>
	<?php }?>
</table>
<?php else:?>
	<p>Não existe SM's com data final ou data inicial no futuro </p>
<?php endif;?>
