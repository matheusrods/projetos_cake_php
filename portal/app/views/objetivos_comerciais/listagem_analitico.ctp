<?php if(isset($listagem) && !empty($listagem)):?>
	<?php
	    echo $paginator->options(array('update' => 'div.lista'));
	?>
<div style="margin-bottom:20px;">
    <strong>Legenda:</strong>&nbsp;
    <span class="badge-empty btn btn-mini btn-warning" title="Exceção de Gestores"></span>&nbsp;Exceção de Gestores
    <span class="badge-empty btn btn-mini btn-info" title="Exceção de Faturamento Médio"></span>&nbsp;Exceção de Faturamento Médio
</div>	
<table class='table table-striped table-bordered' style="max-width:none; white-space:nowrap">
		<thead>
			<th><?php echo $this->Paginator->sort('Mês', 'ObjetivoComercialCliente.mes') ?></th>
			<th><?php echo $this->Paginator->sort('Ano', 'ObjetivoComercialCliente.ano') ?></th>
			<th><?php echo $this->Paginator->sort('Filial', 'ObjetivoComercialCliente.filial_descricao') ?></th>
			<th><?php echo $this->Paginator->sort('Gestor', 'ObjetivoComercialCliente.nome_gestor') ?></th>
			<th class="input-mini"><?php echo $this->Paginator->sort('Produto', 'produto') ?></th>
			<th class="input-mini"><?php echo $this->Paginator->sort('Código', 'codigo_cliente') ?></th>
			<th class="input-mini"><?php echo $this->Paginator->sort('Cliente', 'cliente') ?></th>
			<th class="input-mini"><?php echo $this->Paginator->sort('Visitas', 'visitas') ?></th>
			<th class="input-mini"><?php echo $this->Paginator->sort('Faturamento', 'faturamento') ?></th>
			<th class="input-mini"><?php echo $this->Paginator->sort('Novo Cliente', 'cliente_novo') ?></th>
		</thead>
		<tbody>
			<?php foreach ($listagem as $dado): ?>
				<?php				
				if(isset($dado[0]['excecao']) && $dado[0]['excecao']):
					$classe = 'btn-warning';
				elseif(isset($dado[0]['excecao_faturamento_medio']) && $dado[0]['excecao_faturamento_medio']):
					$classe = 'btn-info';
				else:	
					$classe = '';
				endif;				
				?>
					<tr>
						<td class="<?= $classe;?>"><?= COMUM::anoMes('2014/'.$dado[0]['mes']) ?></td>
						<td class="<?= $classe;?>"><?= $dado[0]['ano'] ?></td>
						<td class="<?= $classe;?>"><?= $dado[0]['filial_descricao'] ?></td>
						<td class="<?= $classe;?>"><?= $dado[0]['nome_gestor'] ?></td>
						<td class="input-mini <?=$classe?>"><?= ($dado[0]['produto_codigo'] != 30) ? $dado[0]['produto_descricao'] : ''?></td>
						<td class="input-mini <?=$classe?>"><?= $dado[0]['codigo_cliente'] ?></td>
						<td class="input-mini <?=$classe?>"><?= $dado[0]['razao_social'] ?></td>
						<td class="input-mini <?=$classe?> numeric"><?= $dado[0]['visitas'] ?></td>
						<td class="input-mini <?=$classe?> numeric"><?= $this->Buonny->moeda($dado[0]['faturamento'], array('nozero' => true)) ?></td>
						<td class="input-mini <?=$classe?>"><?= ($dado[0]['cliente_novo']) ? 'Sim' : 'Não' ?></td>
					</tr>
			<?php endforeach ?>
		</tbody>	
	</table>
	<div class='row-fluid'>
	    <div class='numbers span6'>
	    	<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	        <?php echo $this->Paginator->numbers(); ?>
	    	<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	    </div>
	    <div class='counter span6'>
	        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages% - Total de %count%')); ?>
	    </div>
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
	<div class="alert">Nenhum registro encontrado</div>
<?php endif;?>