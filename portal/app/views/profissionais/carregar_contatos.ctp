<?php if (empty($historico_sinistros)): ?>
	<?php echo $this->BForm->create('Profissional', array('autocomplete' => 'off', 'url' => array('controller' => 'profissionais', 'action' => 'score'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('codigo_documento', array('label' => false, 'placeholder' => 'CPF', 'class' => 'input-small just-number cpf', 'type' => 'text')); ?>
		</div>
		<?php echo $this->BForm->submit('Filtrar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
	<?php //echo $this->Javascript->codeBlock("jQuery(document).ready(function() {setup_mascaras()})") ?>
<?php else: ?>
	<div class='well'>
		<strong>CPF: </strong><?= $profissional['Profissional']['codigo_documento'] ?> - <?= $profissional['Profissional']['nome'] ?>
	</div>
	<h4>Score Profissional por Sinistro</h4>
	<table class='table table-striped'>
		<thead>
			<th>Questao</th>
			<th>Resposta</th>
			<th class='numeric'>Percentual Histórico</th>
		</thead>
		<?php $qtd_questoes = 0 ?>
		<?php $total = 0 ?>
		<?php foreach ($ficha_pesquisa_q_r as $qr): ?>
			<?php $qtd_questoes ++ ?>
			<?php $total += 1 * $qr['percentual'] ?>
			<tr>
				<td><?= $qr['Questao']['descricao'] ?></td>
				<td><?= $qr['Resposta']['descricao'] ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($qr['percentual'], array('places' => 1)).'%' ?></td>
			</tr>
		<?php endforeach ?>
		<tfoot>
			<tr>
				<td>Risco</td>
				<td></td>      
				<td class='numeric'><?= $this->Buonny->moeda($total / $qtd_questoes, array('places' => 1)).'%' ?></td>
			</tr>
		</tfoot>
	</table>
	<h4>Score Profissional por Teleconsult</h4>
	<table class='table table-striped'>
		<thead>
			<th>Questao</th>
			<th>Resposta</th>
			<th class='numeric'>Percentual Histórico</th>
		</thead>
		<?php $qtd_questoes = 0 ?>
		<?php $total = 0 ?>
		<?php foreach ($ficha_pesquisa_q_r_tlc as $qr): ?>
			<?php $qtd_questoes ++ ?>
			<?php $total += 1 * $qr['percentual'] ?>
			<tr>
				<td><?= $qr['Questao']['descricao'] ?></td>
				<td><?= $qr['Resposta']['descricao'] ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($qr['percentual'], array('places' => 1)).'%' ?></td>
			</tr>
		<?php endforeach ?>
		<tfoot>
			<tr>
				<td>Risco</td>
				<td></td>
				<td class='numeric'><?= $this->Buonny->moeda($total / $qtd_questoes, array('places' => 1)).'%' ?></td>
			</tr>
		</tfoot>
	</table>
	<h4>Geral por Sinistro</h4>
	<table class='table table-striped'>
		<thead>
			<th>Questão</th>
			<th>Resposta</th>
			<th class='numeric'>Qtd Total</th>
			<th class='numeric'>Qtd</th>
			<th class='numeric'>Percentual</th>
		</thead>
		<?php foreach ($historico_sinistros as $sinistro): ?>
			<tr>
				<td><?= $sinistro[0]['descricao_questao'] ?></td>
				<td><?= $sinistro[0]['descricao_resposta'] ?></td>
				<td class='numeric'><?= $sinistro[0]['qtd_questao'] ?></td>
				<td class='numeric'><?= $sinistro[0]['qtd'] ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($sinistro[0]['percentual'], array('places' => 1)).'%' ?></td>
			</tr>
		<?php endforeach ?>
	</table>
	<h4>Geral por Teleconsult</h4>
	<table class='table table-striped'>
		<thead>
			<th>Questão</th>
			<th>Resposta</th>
			<th class='numeric'>Qtd Total</th>
			<th class='numeric'>Qtd</th>
			<th class='numeric'>Percentual</th>
		</thead>
		<?php foreach ($historico_teleconsults as $sinistro): ?>
			<tr>
				<td><?= $sinistro[0]['descricao_questao'] ?></td>
				<td><?= $sinistro[0]['descricao_resposta'] ?></td>
				<td class='numeric'><?= $sinistro[0]['qtd_questao'] ?></td>
				<td class='numeric'><?= $sinistro[0]['qtd'] ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($sinistro[0]['percentual'], array('places' => 1)).'%' ?></td>
			</tr>
		<?php endforeach ?>
	</table>
<?php endif ?>