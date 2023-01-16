<?php
    echo $paginator->options(array('update' => 'div.lista'));
?>
<?php if(empty($dados)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
	<table class='table table-striped table-bordered alvos'>
		<thead>
			<tr>
				<th>Usuário</th>
				<th>IP</th>
				<th>Data Ponto</th>
				<th>Data Registro</th>
				<th>Tipo de Registro</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($dados as $dado): ?>
				<tr>
					<td><?= $usuarios[$dado['PontoEletronico']['codigo_usuario']]; ?></td>
					<td><?= $dado['PontoEletronico']['numero_ip']; ?></td>
					<td><?= AppModel::dbDateToDate($dado['0']['hora_ponto']); ?></td>
					<td><?= $dado['PontoEletronico']['created']; ?></td>
					<td><?= $dado['TipoPontoEletronico']['descricao_ponto_eletronico']; ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
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
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$.placeholder.shim();
		setup_mascaras();

		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
		jQuery("table.alvos").tablesorter()
    });', false);
?>