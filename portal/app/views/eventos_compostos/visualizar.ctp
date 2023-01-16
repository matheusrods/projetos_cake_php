<div class="well">
	<strong>Cliente: </strong><?= DbbuonnyGuardianComponent::converteClienteGuardianEmBuonny($dados[0]['TPjurPessoaJuridica']['pjur_pess_oras_codigo']);?> - <?= $dados[0]['TPjurPessoaJuridica']['pjur_razao_social']?>
</div>
<div class="row-fluid inline">
	<p><strong>Descrição : </strong><?= $dados[0]['TEcomEventoComposto']['ecom_descricao']?></p>
	<p><strong>Abrangência em Minutos : </strong><?= $dados[0]['TEcomEventoComposto']['ecom_minutos_abrangencia']?></p>
	<p><strong>Evento Sequencial: </strong><?= ($dados[0]['TEcomEventoComposto']['ecom_minutos_abrangencia'] == 'S') ? 'Sim' : 'Não'?></p>
</div>
<table class="table table-striped">
	<thead>
		<th>Evento</th>
		<th class="numeric">Ordem</th>
	</thead>
	<tbody>
		<?php foreach ($dados as $key => $composicoes_eventos) :?>
			<tr>
				<td><?= $composicoes_eventos['TEspaEventoSistemaPadrao']['espa_descricao']?></td>
				<td class="numeric"><?= $composicoes_eventos['TCeveComposicaoEvento']['ceve_ordem']?></td>
			</tr>
		<?php endforeach;?>	
	</tbody>
</table>	
<div class="form-actions">
	<?php echo $html->link('Voltar',array('action'=>'index') , array('class' => 'btn')); ?>
</div>