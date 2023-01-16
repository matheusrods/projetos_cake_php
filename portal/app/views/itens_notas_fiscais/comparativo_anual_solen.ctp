<div class='well'>
	<?php echo $this->BForm->create('Notaite', array('url' => array('controller' => 'itens_notas_fiscais', 'action' => 'comparativo_anual_solen'))); ?>
	<?php echo $this->element('itens_notas_fiscais/fields_filtros_solen'); ?>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
</div>
<?php if (isset($dados)): ?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Mês</th>
				<th class="numeric"><?= ($this->data['Notaite']['ano']-1)."(R$)"; ?></th>
				<th class="numeric"><?= ($this->data['Notaite']['ano'])."(R$)"; ?></th>
				<th class="numeric">Diferença(%)</th>
			</tr>
		</thead>
		<tbody>
			<?php $total_ano1 = 0 ?>
			<?php $total_ano2 = 0 ?>
			<?php $total_ate_mes1 = 0 ?>
			<?php $total_ate_mes2 = 0 ?>
			<?php $ate_mes = '' ?>
			<?php foreach($dados as $key => $dado): ?>
				<?php $total_ano1 += $dado['Ano1']['valor'] ?>
				<?php $total_ano2 += $dado['Ano2']['valor'] ?>
				<?php if ($key < date('m')): ?>
					<?php $ate_mes = $dado['Mes']['nome'] ?>
					<?php $total_ate_mes1 += $dado['Ano1']['valor'] ?>
					<?php $total_ate_mes2 += $dado['Ano2']['valor'] ?>
				<?php endif ?>
				<tr>
					<td><?= $dado['Mes']['nome'] ?></td>
					<td class="numeric"><?= $this->Buonny->moeda($dado['Ano1']['valor'], array('places' => 2, 'nozero' => true)) ?></td>
					<td class="numeric"><?= $this->Buonny->moeda($dado['Ano2']['valor'], array('places' => 2, 'nozero' => true)) ?></td>
					<td class="numeric <?= (($dado['0']['diferenca']<0) ? 'negative_value' : '') ?>"><?= $this->Buonny->moeda($dado['0']['diferenca'], array('places' => 2, 'nozero' => true)) ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<?php $total_diferenca = (($total_ano2 / $total_ano1) - 1) * 100  ?>
			<?php $total_diferenca_ate_mes = (($total_ate_mes2 / $total_ate_mes1) - 1) * 100 ?>
			<tr>
				<td>Total até <?= $ate_mes ?></td>
				<td class="numeric"><?= $this->Buonny->moeda($total_ate_mes1, array('places' => 2, 'nozero' => true)) ?></td>
				<td class="numeric"><?= $this->Buonny->moeda($total_ate_mes2, array('places' => 2, 'nozero' => true)) ?></td>
				<td class="numeric <?= (($total_diferenca_ate_mes<0) ? 'negative_value' : '') ?>"><?= $this->Buonny->moeda($total_diferenca_ate_mes, array('places' => 2, 'nozero' => true)) ?></td>
			</tr>
			<tr>
				<td>Total Geral</td>
				<td class="numeric"><?= $this->Buonny->moeda($total_ano1, array('places' => 2, 'nozero' => true)) ?></td>
				<td class="numeric"><?= $this->Buonny->moeda($total_ano2, array('places' => 2, 'nozero' => true)) ?></td>
				<td class="numeric <?= (($total_diferenca<0) ? 'negative_value' : '') ?>"><?= $this->Buonny->moeda($total_diferenca, array('places' => 2, 'nozero' => true)) ?></td>
			</tr>
		</tfoot>
	</table>
<?php endif ?>