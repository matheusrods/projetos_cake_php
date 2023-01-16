<?php if(isset($listagem) && !empty($listagem)):?>
	<?php
	    echo $paginator->options(array('update' => 'div.lista'));
	?>
	<div class='row-fluid inline'>
		<table class='table table-striped tablesorter'>
			<thead>
				<th class="input-small">SM</th>
				<th class="input-large">Embarcador</th>
				<th class="input-large">Transportador</th>
				<th class="input-large">Origem</th>
				<th class="input-large">Destino</th>
				<th class="input-large">Previsão Inicio</th>
				<th class="input-large">Previsão Fim</th>
				<th class="input-large">Data Inicio</th>
				<th class="input-large">Placa</th>
				<th class="input-large">Estação</th>
				<th class="input-large">Motorista</th>
				<th class="input-large">Tecnologia</th>
			</thead>
			<tbody>
				<?php foreach ($listagem as $dados):?>
					<tr>
						<td>
							<?= $this->Buonny->codigo_sm($dados['0']['codigo_sm']) ?>
						</td>
						<td>
							<?= $dados['TPjurEmbarcador']['pjur_razao_social'] ?>
						</td>
						<td>
							<?= $dados['TPjurTransportador']['pjur_razao_social'] ?>
						</td>
						<td>
							<?= $dados['TRefOrigem']['refe_descricao'] ?>
						</td>
						<td>
							<?= $dados['TRefDestino']['refe_descricao'] ?>
						</td>
						<td>
							<?= $dados['TViagViagem']['viag_previsao_inicio'] ?>
						</td>
						<td>
							<?= $dados['TViagViagem']['viag_previsao_fim'] ?>
						</td>
						<td>
							<?= $dados['TViagViagem']['viag_data_inicio'] ?>
						</td>
						<td>
							<?= $dados['TVeicVeiculo']['veic_placa'] ?>
						</td>
						<td>
							<?= $dados['TErasEstacaoRastreamento']['eras_descricao'] ?>
						</td>
						<td>
							<?= $dados['TPessPessoa']['pess_nome'] ?>
						</td>
						<td>
							<?= $dados['TTecnTecnologia']['tecn_descricao'] ?>
						</td>
					</tr>
				<?php endforeach;?>
			</tbody>			
		</table>	
	</div>
	<div class='row-fluid'>
	    <div class='numbers span6'>
	    	<?= $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	        <?= $this->Paginator->numbers(); ?>
	    	<?= $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	    </div>
	    <div class='counter span6'>
	        <?= $this->Paginator->counter(array('format' => 'Página %page% de %pages% - Total de %count%')); ?>
	    </div>
	</div>
	<?= $this->Js->writeBuffer(); ?>
<?php else:?>
	<div class="alert">Nenhum registro encontrado</div>
<?php endif;?>