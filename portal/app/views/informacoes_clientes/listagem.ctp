<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?= $this->Paginator->sort('Código', 'codigo') ?></th>
            <th><?= $this->Paginator->sort('Nome', 'razao_social') ?></th>
            <th><?= $this->Paginator->sort('Área BuonnySat', 'AreaAtuacao.descricao') ?></th>
            <th><?= $this->Paginator->sort('Sistema Monitoramento', 'codigo_sistema_monitoramento') ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
			<?php foreach ($informacoes_clientes as $informacao_cliente): ?>
        <tr>
					<td class="input-mini">
						<?= $informacao_cliente['InformacaoCliente']['codigo'] ?>
					</td>
					<td>
						<?= $informacao_cliente['InformacaoCliente']['razao_social'] ?>
					</td>
					<td>
						<?= $informacao_cliente['AreaAtuacao']['descricao'] ?>
					</td>
					<td>
						<?= SistemaMonitoramento::descricao($informacao_cliente['InformacaoCliente']['codigo_sistema_monitoramento']) ?>
					</td>
					<td class="pagination-centered">
						<?= $html->link('', array('action' => 'editar', $informacao_cliente['InformacaoCliente']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
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
<?php echo $this->Js->writeBuffer(); ?>
