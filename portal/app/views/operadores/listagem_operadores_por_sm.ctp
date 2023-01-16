<?php if( $estacao ): ?>
<div class="well">
	<strong>Estação: </strong><?=$estacao?>
</div>
<?php endif;?>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th class="input-medium">Operador</th>
			<th colspan="3"></th>
			<th style="width:8px"></th>			
			<th class="input-medium">Operador</th>
			<th colspan="3"></th>
		</thead>
		<tbody>
			<?php foreach ($listagem as $grupo => $linhas):?>
				<?php foreach ($linhas as $key => $usuario):?>
				<tr>
					<td><?php echo $usuario[0]['TUsuaUsuario']['usua_login'] ?></td>
					<td colspan="3"></td>
					<?php if(isset($usuario[1])): ?>
						<td></td>
						<td><?php echo $usuario[1]['TUsuaUsuario']['usua_login'] ?></td>
						<td colspan="3"></td>
					<?php else: ?>
						<td colspan="4">&nbsp;</td>
					<?php endif; ?>					
				</tr>
				<?php endforeach;?>
			<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="12">				
					<h5>Total: <?php echo $total_operadores?></h5>
				</td>
			</tr>
		</tfoot>
	</table>
</div>