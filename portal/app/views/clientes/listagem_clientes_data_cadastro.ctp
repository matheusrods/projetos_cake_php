 <?php 
    echo $paginator->options(array('update' => 'div#clientes_dados_gerais')); 
?>


 <table class="table table-condensed table-striped">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
			<th><?php echo $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
			<th><?php echo $this->Paginator->sort('Razão Social','razao_social') ?></th>
			<th><?php echo $this->Paginator->sort('Nome Fantasia', 'nome_fantasia')?></th>
			<th><?php echo $this->Paginator->sort('Corretora', 'Corretora.nome')?></th>
			<th><?php echo $this->Paginator->sort('Ativo', 'ativo')?></th>
			<th><?php echo $this->Paginator->sort('Usuário', 'Usuario.apelido')?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($clientes_data_cadastro as $cliente_data_cadastro): ?>
			<tr id='<?= $cliente_data_cadastro['Cliente']['codigo'] ?>' class='evt-carregar-dado'>
				<td>
					<?= $cliente_data_cadastro['Cliente']['codigo'] ?>
				</td>
				<td>
					<?= $buonny->documento($cliente_data_cadastro['Cliente']['codigo_documento']) ?>
				</td>
				<td>
					<?= $cliente_data_cadastro['Cliente']['razao_social'] ?>
				</td>
				<td>
					<?= $cliente_data_cadastro['Cliente']['nome_fantasia'] ?>
				</td>
				<td>
					<?= $cliente_data_cadastro['Corretora']['nome'] ?>
				</td>
				<td>
				   <?= $cliente_data_cadastro['Cliente']['ativo'] == 1 ? 'ativo' : 'inativo'; ?>
				</td>
				<td>
					<?= $cliente_data_cadastro['Usuario']['apelido'] ?>
				</td>
				<td>
					<?= $html->link('', 'javascript:void(0)', array('class' => 'icon-search evt-carregar-dado', 'title' => 'Visualizar logs')) ?>
				</td>
			</tr>
		<?php endforeach; ?>        
	</tbody>
</table>

<div class='row-fluid'>
    <div class='numbers span6'>
        <?php // PARA PAGINACAO
        echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
        <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>

<?php echo $this->Js->writeBuffer(); ?>
