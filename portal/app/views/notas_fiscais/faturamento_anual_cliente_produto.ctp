<div class='well'>
    <span class="pull-right">
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export', $this->data['Notafis']['ano'], $this->data['Notafis']['mes_cadastro_cliente']), array('escape' => false, 'title' =>'Exportar para Excel'));?>   
    </span>
</div>
<table class='table table-striped'>
	<thead>
		<th>Código</th>
		<th>Cliente</th>
		<th>Produto</th>
		<th class='numeric'>Jan</th>
		<th class='numeric'>Fev</th>
		<th class='numeric'>Mar</th>
		<th class='numeric'>Abr</th>
		<th class='numeric'>Mai</th>
		<th class='numeric'>Jun</th>
		<th class='numeric'>Jul</th>
		<th class='numeric'>Ago</th>
		<th class='numeric'>Set</th>
		<th class='numeric'>Out</th>
		<th class='numeric'>Nov</th>
		<th class='numeric'>Dez</th>
		<th class='numeric'>Total</th>
	</thead>
	<tbody>
		<?php $jan = 0 ?>
		<?php $fev = 0 ?>
		<?php $mar = 0 ?>
		<?php $abr = 0 ?>
		<?php $mai = 0 ?>
		<?php $jun = 0 ?>
		<?php $jul = 0 ?>
		<?php $ago = 0 ?>
		<?php $set = 0 ?>
		<?php $out = 0 ?>
		<?php $nov = 0 ?>
		<?php $dez = 0 ?>
		<?php $total = 0 ?>
		<?php $ultimo_cliente = '' ?>
		<?php $total_clientes = 0 ?>
		<?php foreach ($dados as $dado): ?>
			<?php if ($ultimo_cliente != $dado['Cliente']['codigo']): ?>
				<?php $ultimo_cliente = $dado['Cliente']['codigo'] ?>
				<?php $total_clientes++ ?>
			<?php endif ?>
			<?php $jan += $dado[0]['Jan'] ?>
			<?php $fev += $dado[0]['Fev'] ?>
			<?php $mar += $dado[0]['Mar'] ?>
			<?php $abr += $dado[0]['Abr'] ?>
			<?php $mai += $dado[0]['Mai'] ?>
			<?php $jun += $dado[0]['Jun'] ?>
			<?php $jul += $dado[0]['Jul'] ?>
			<?php $ago += $dado[0]['Ago'] ?>
			<?php $set += $dado[0]['Set'] ?>
			<?php $out += $dado[0]['Out'] ?>
			<?php $nov += $dado[0]['Nov'] ?>
			<?php $dez += $dado[0]['Dez'] ?>
			<?php $total += $dado[0]['total_faturado'] ?>
			<tr>
				<td class='input-mini'><?= $dado['Cliente']['codigo'] ?></td>
				<td style='max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'><?= $dado['Cliente']['razao_social'] ?></td>
				<td style='max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'><?= $dado['NProduto']['descricao'] ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Jan'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Fev'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Mar'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Abr'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Mai'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Jun'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Jul'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Ago'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Set'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Out'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Nov'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['Dez'], array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dado[0]['total_faturado'], array('nozero' => true)) ?></td>
			</tr>
		<?php endforeach; ?>
		<tfoot>
			<tr>
				<td>Total</td>
				<td class='numeric'><?= $this->Buonny->moeda($total_clientes, array('nozero' => true, 'places' => 0)) ?></td>
				<td></td>
				<td class='numeric'><?= $this->Buonny->moeda($jan, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($fev, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($mar, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($abr, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($mai, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($jun, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($jul, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($ago, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($set, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($out, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($nov, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($dez, array('nozero' => true)) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($total, array('nozero' => true)) ?></td>
			</tr>
		</tfoot>
	<Dezody>
</table>