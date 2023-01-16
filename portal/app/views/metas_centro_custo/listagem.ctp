<?=$paginator->options(array('update' => 'div.lista'));?>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Grupo', 'grupo_empresa') ?></th>
            <th><?= $this->Paginator->sort('Empresa', 'nome_empresa') ?></th>
            <th><?= $this->Paginator->sort('Centro de Custo', 'centro_custo') ?></th>
            <th><?= $this->Paginator->sort('Fluxo', 'codigo_fluxo') ?></th>
            <th><?= $this->Paginator->sort('Sub Fluxo', 'codigo_sub_fluxo') ?></th>
            <th><?= $this->Paginator->sort('Mês Referência', 'ano_mes') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Meta (R$) ', 'valor_meta') ?></th>
            <th class='action-icon'>&nbsp;</th>
            <th class='action-icon'>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($listagem as $key => $meta): ?>
        <tr>
            <td><?= $grupo_empresa->descricao($meta['MetaCentroCusto']['grupo_empresa']); ?></td>
            <td><?= $meta['MetaCentroCusto']['nome_empresa'] ?></td>
            <td><?= $meta['MetaCentroCusto']['centro_custo'] ?> - <?= $meta['CentroCusto']['descricao'] ?></td>
            <td><?= $meta['Grflux']['descricao'] ?></td>
            <td><?= $meta['Sbflux']['descricao'] ?></td>
            <td><?= comum::anoMes( date('Y-m', strtotime($meta['MetaCentroCusto']['ano_mes'].'01'))).'/'. substr($meta['MetaCentroCusto']['ano_mes'], 0, 4);?></td>
            <td class="numeric"><?= $this->Buonny->moeda( $meta['MetaCentroCusto']['valor_meta'] ); ?></td>
            <td class="action-icon">
                <?php echo $this->Html->link('<i class="icon-edit"></i>',
                array('action' => 'editar', $meta['MetaCentroCusto']['codigo'] ), array('escape' => false, 'title' =>'Editar Meta' ));?>
            </td>
			<td class="action-icon">
				<?php echo $html->link('', array('controller' => 'metas_centro_custo', 'action' => 'excluir', 
				$meta['MetaCentroCusto']['codigo'] ), array('class' => 'icon-trash', 'title' => 'Excluir Meta'), 
				'Confirma exclusão?'); ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
        <tfoot>
            <tr>
                <td colspan="10"><strong>Total</strong>
                	<?=$this->Paginator->params['paging']['MetaCentroCusto']['count']; ?>
                </td>
            </tr>
        </tfoot>
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