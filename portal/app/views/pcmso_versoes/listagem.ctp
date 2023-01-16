<?php if(isset($listagem) && count($listagem)) : ?>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>

	<div class="row-fluid inline">
		<?php echo $this->BForm->create('ClienteFuncionario', array('type' => 'post' ,'url' => array('controller' => 'pedidos_exames','action' => 'inclusao_em_massa', $codigo_grupo_economico))); ?>

		<button id="botao" type=submit class="btn btn-success btn-lg" style="display: none;"><i class="glyphicon glyphicon-share"></i> Aplicar </button>

		<table class="table table-striped">
			<thead>
				<tr>
					<th style="width:5%">Código</th>
					<th style="width:25%">Unidade</th>
					<th style="width:10%">Versão</th>
					<th style="width:15%">Medico Coordenador</th>
					<th style="width:10%">Início Vigência</th>
					<th style="width:10%">Perído Vigência</th>
					<th style="width:10%">Final Vigência</th>
					<th style="width:5%">Ação</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($listagem as $key => $linha): ?>
					<tr>
						<td class="input-mini"><?= $linha['PcmsoVersoes']['codigo']; ?></td>
						<td><?= $linha['GrupoEconomico']['descricao']; ?></td>
						<td><?= $linha['PcmsoVersoes']['versao']; ?></td>
						<td>
							<?= empty($linha['Medicos']['nome']) ? 'Não consta' : $linha['Medicos']['nome']; ?>
						</td>
						<td><?= $linha['PcmsoVersoes']['inicio_vigencia_pcmso']; ?></td>
						<td><?= $linha['PcmsoVersoes']['periodo_vigencia_pcmso']; ?></td>
						<td><?= date('d/m/Y', strtotime($linha[0]['final_vigencia_pcmso'])); ?></td>
						<td>
							<?= $html->link('', array('controller' => 'pcmso_versoes', 
								'action' => 'imprimir_relatorio', 
								$linha['PcmsoVersoes']['codigo_cliente_alocacao'], 
								$linha['PcmsoVersoes']['codigo']), 
							array('class' => 'icon-print',
								'data-toggle' => 'tooltip', 
								'title' => 'Imprimir relatório')
						); 
						?>
					</td>
				</tr>
			<?php endforeach; ?>        
		</tbody>

	</table>
	<div class='row-fluid'>
		<div class='numbers span6'>
			<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
			<?php echo $this->Paginator->numbers(); ?>
			<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
		</div>
		<div class='counter span6'>
			<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
		</div>
	</div> 
	<?php echo $this->BForm->end(); ?>
</div>
<?php echo $this->Js->writeBuffer(); ?>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>
<?php echo $this->Javascript->codeBlock('
	function mostra_botao(element) {
		if($(element).val()) {
			$("#botao").show();
		} else {
			$("#botao").hide();
		}
	}
	'); ?>