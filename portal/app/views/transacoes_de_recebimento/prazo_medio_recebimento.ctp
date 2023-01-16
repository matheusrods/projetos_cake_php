<div class='well'>
<?php echo $this->BForm->create('Tranrec', array('url' => array('controller' => 'transacoes_de_recebimento', 'action' => 'prazo_medio_recebimento'))); ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('ano', array('label' => false, 'placeholder' => 'Ano','class' => 'input-small', 'type' => 'select', 'options' => $anos)) ?>
	<?php echo $this->Buonny->input_grupo_empresas($this,$grupos_empresas, $empresas); ?>
</div>
<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
<?php echo $this->BForm->end() ?>
</div>
<?php if(isset($dados)):?>
<div class="well">
	<strong>Grupo: </strong><?php echo $nome_grupo; ?>
	<strong>Empresa: </strong><?php echo (!empty($empresa) ? $empresa['LojaNaveg']['razaosocia'] : 'Todas empresas'); ?>
	<?php if (!empty($cliente)): ?>
		<strong>Código: </strong><?php echo $cliente['Cliente']['codigo']; ?>
		<strong>Cliente: </strong><?php echo $cliente['Cliente']['razao_social']; ?>
	<?php endif ?>
</div>
	<?php if(!empty($dados)):?>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th class="numeric"><?php echo 'Ano '.substr($dados[0][0]['ano_mes'], 3)?></th>
					<th class="numeric">Dias Médio Emissão</th>
					<th class="numeric">Dias Médio Pagamento</th>
					<th class="numeric">Número de Títulos</th>
				</tr>
			</thead>
			<tbody>
				<?php $total_titulos = 0; ?>
				<?php $media_dias = 0; ?>
				<?php $media_pagamento = 0; ?>
				<?php foreach($dados as $dado): ?>
					<tr>
						<td><?php echo $this->Buonny->mes_extenso(substr($dado[0]['ano_mes'], 0, 2)); ?></td>
						<td class="numeric"><?php echo $dado[0]['dias_medio']?></td>
						<td class="numeric"><?php echo $dado[0]['pagamento_medio']?></td>
						<td class="numeric"><?php echo $dado[0]['qtd_titulos']?></td>
					</tr>
				<?php $total_titulos += $dado[0]['qtd_titulos'] ?>
				<?php $media_dias += $dado[0]['dias_medio'] ?>
				<?php $media_pagamento += $dado[0]['pagamento_medio'] ?>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td>
						<strong>Total</strong>
					</td>
					<td class="numeric">
						<?php echo (int)($media_dias / count($dados)) ?>
					</td>
					<td class="numeric">
						<?php echo (int)($media_pagamento / count($dados)) ?>
					</td>
					<td class="numeric">
						<?php echo $total_titulos ?>
					</td>
				</tr>
			</tfoot>
		</table>
	<?php endif; ?>
<?php endif; ?>