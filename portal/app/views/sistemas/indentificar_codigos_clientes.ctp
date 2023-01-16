<div class='form-procurar'>	
	<div class='well'>
    	<div>
		    <?php echo $this->BForm->create('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'sistemas', 'action' => 'indentificar_codigos_clientes'))) ?>
		    <div class="row-fluid inline">
	            <?php echo $this->Buonny->input_codigo_cliente($this) ?>
		    </div>
		    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		    <?php echo $this->BForm->end();?>
		</div>
	</div>
</div>
<?php if (isset($clientes_dbbuonny)): ?>
	<h5>Portal / Informações</h5>
	<table class='table table-striped'>
		<thead>
			<th>Código</th>
			<th>CNPJ</th>
			<th>Razão Social</th>
			<th>SubTipo</th>
			<th>Status</th>
			<th>BuonnySat</th>
		</thead>
		<tbody>
			<?php $contador = 0 ?>
			<?php foreach ($clientes_dbbuonny as $cliente): ?>
				<?php $contador++ ?>
				<tr>
					<td><?= $cliente['Cliente']['codigo'] ?></td>
					<td><?= $this->Buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
					<td><?= $cliente['Cliente']['razao_social'] ?></td>
					<td><?= $cliente['ClienteSubTipo']['descricao'] ?></td>
					<?php $ativo_inativo = ($cliente['Cliente']['ativo'] == 1 ? 'Ativo' : 'Inativo')?>
					<td><a title="<?=$ativo_inativo?>" href="#"><spam class ="<?= ($cliente['Cliente']['ativo'] == 1 ? 'badge-empty badge badge-success' : 'badge-empty badge badge-important')?>"></spam>
					</td>
				 	<?php $codigo=($cliente['ClienteProduto']['codigo_motivo_bloqueio']==1?'Desbloqueado':'Bloqueado')?>
					<td> <a title="<?=$codigo ?>" href="#"> <spam  class="<?=($cliente['ClienteProduto']['codigo_motivo_bloqueio']==1 ? 'badge-empty badge badge-success':'badge-empty badge badge-important')?>"></spam>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td>Total</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td class='numeric'><?= $contador ?></td>
			</tr>
		</tfoot>
	</table>
<?php endif ?>
<?php if (isset($clientes_monitora)): ?>
	<h5>Monitora</h5>
	<table class='table table-striped'>
		<thead>
			<th>Código</th>
			<th>CNPJ</th>
			<th>Razão Social</th>
			<th>Tipo</th>
			<th>Status</th>
			<th>Bloqueio</th>
		</thead>
		<tbody>
			<?php $contador = 0 ?>
			<?php foreach ($clientes_monitora as $cliente): ?>
				<?php $contador++ ?>
				<tr>
					<td><?= $cliente['ClientEmpresa']['Codigo'] ?></td>
					<td><?= $cliente['ClientEmpresa']['CNPJCPF'] ?></td>
					<td><?= $cliente['ClientEmpresa']['Raz_Social'] ?></td>
					<td><?= strtoupper(ClientEmpresa::descricaoTipoEmpresa($cliente['ClientEmpresa']['TipoEmpresa'])) ?></td>
					<?php $status1 = ($cliente['ClientEmpresa']['Status'] == 'S' ? 'Ativo' : 'Inativo')?>
					<td><a title="<?=$status1 ?>" href="#"><spam class="<?= ($cliente['ClientEmpresa']['Status'] == 'S' ? 'badge-empty badge badge-success' : 'badge-empty badge badge-important')?>"></spam>
					</td>
				<?php $Bloq_Finac = ($cliente['ClientEmpresa']['BloqFinanc'] == 'S' ? 'Bloqueado' : 'Desbloqueado')?>
				<td><a title="<?=$Bloq_Finac ?>" href="#"><spam class="<?= ($cliente['ClientEmpresa']['BloqFinanc'] == 'S' ? 'badge-empty badge badge-important' :'badge-empty badge badge-success')?>"></spam>
				</td> 
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td>Total</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td class='numeric'><?= $contador ?></td>
			</tr>
		</tfoot>
	</table>
<?php endif ?>