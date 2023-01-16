
<?php if (!empty($codigo_cliente)) :  ?>
	<?php include("modal.ctp") ?>
	<div class='actionbar-right '>
		<button class="btn btn-success" id="open-save-modal">
			<i class="icon-plus icon-white"></i> Novo
		</button>
	</div>
<?php endif; ?>

<?php if (count($data) > 0) :  ?>
	<?php
	echo $paginator->options(array('update' => 'div.lista'));
	$total_paginas = $this->Paginator->numbers();
	?>
	<div class="store-page">

		<table class="table table-striped">
			<thead>
				<tr>
					<th class="input-small">Cliente</th>
					<th class="input-small">Nome</th>
					<th class="input-small">Status</th>
					<th class="input-medium" title="Contagem com o cabeçalho e eventuais linhas em branco.">Quantidade de linhas arquivo <img class="icon-info-sign"> </th>
					<th class="input-medium">Quantidade de linhas arquivo carregadas</th>
					<th class="input-medium">Quantidade de linhas processadas</th>
					<th class="input-medium">Quantidade de linhas com erro</th>
					<th style='width:75px'>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data as $record) : ?>
					<?php $row = (object) $record['IntUploadCliente'] ?>
					<tr style="width: 100%">
						<td class="input-mini" style="min-width: 40%;">
							<?= $row->codigo_cliente  ?>
						</td>
						<td class="input-mini">
							<?= $row->nome_arquivo  ?>
						</td>
						<td class="input-mini">
							<?= $record['StatusTransferencia']['descricao']; ?>
						</td>
						<td class="input-mini" style="min-width: 40%;">
							<?= $row->qtd_linhas  ?>
						</td>
						<td class="input-mini" style="min-width: 40%;">
							<?= $row->qtd_linhas_processadas  ?>
						</td>
						<td class="input-mini" style="min-width: 40%;">
							<?= $record[0]['total_processado']  ?>
						</td>
						<td class="input-mini" style="min-width: 40%;">
							<?= $record[0]['erro']  ?>
						</td>
						<td>
							<?php if ($row->ativo) : ?>
								<?= $this->Html->link('', array('action' => 'troca_status', $row->codigo), array('title' => 'Inativar', 'class' => 'icon-random  change-status')); ?>
								<span class="badge-empty badge badge-success" title="Ativo"></span>
							<?php else : ?>
								<?= $this->Html->link('', array('action' => 'troca_status', $row->codigo), array('title' => 'Ativar', 'class' => 'icon-random change-status')); ?>
								<span class="badge-empty badge badge-important" title="Inativo"></span>
							<?php endif; ?>
							
							<?= $this->Html->link('', array('action' => 'download', $row->codigo), array('title' => 'Download', 'class' => 'icon-download',  'target' => '_blank')); ?>

							<?php
							if(!empty($record[0]['erro'])) {
								echo $this->Html->link('', array('action' => 'download_erros', $row->codigo), array('title' => 'Download erros', 'class' => 'icon-download',  'target' => '_blank', "style"=>"border: 1px solid; text-decoration: none; border-radius: 100%; padding: 1px; background-position: -167px -95px; background-color: #ff000073;")); 
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<div class='row-fluid'>
			<div class='numbers span6'>
				<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'paginacao_anterior')); ?>
				<?php echo $this->Paginator->numbers(); ?>
				<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'paginacao_proximo')); ?>
			</div>
			<div class='counter span6'>
				<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
			</div>
		</div>
	</div>
<?php else : ?>
	<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;  ?>
<script src="/portal/js/layouts/listagem.js"></script>
<script src="/portal/js/layouts/importacao.js"></script>