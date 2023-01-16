<div class="row-fluid inline">
<?if ($codigo_usuario) :?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th colspan="2">
				<!-- Responsável-->
				<?php echo $dados['resumo'][$codigo_usuario][0]['apelido'];?> - <?php echo $data;?>
				</th>
			</tr>
		</thead>
		<?php if(!empty($dados['resumo'])):?>
			<tr>
			<?php foreach( $dados['resumo'][$codigo_usuario] as $key => $value ):?> 
				<td class='input-small'>
					<b><?php echo strtoupper($value['nivel']);?>: </b><?php echo strtoupper($value['quantidade']);?>
				</td>
			</tr>
			<?php endforeach;?>
		<?php endif;?>
	</table>
<?else:?>
	<?php foreach( $dados as $key => $value ):?>
	<table class="table table-striped">
		<thead>
		<tr>
			<th colspan="2">Pesquisas</th>
		</tr>
		</thead>
		<?php foreach( $value as $dados_nivel ):?>
		<tr>
			<td class='input-small'>
				<b><?php echo strtoupper($dados_nivel['nivel']);?>: </b><?php echo strtoupper($dados_nivel['quantidade']);?>
			</td>
		</tr>
		<?php endforeach;?>
	</table>
	<?php endforeach;?>
<?endif;?>
</div>