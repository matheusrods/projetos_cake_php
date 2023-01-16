<?php if (!empty($contratos)): ?>
	<div class="well">
		<table class="table table-striped">
   			<thead>
        		<tr>
            		<th>Código Cliente</th>
            		<th>Cliente</th>
            		<th>Produto</th>
            		<th>Data Vencimento</th>
            	</tr>
            </thead>
        	<tbody>
        		<?php foreach ($contratos as $contrato): ?>
            		<tr>
                		<td><?= $contrato['Cliente']['codigo']?></td>
                		<td><?= $contrato[0]['razao_social']?></td>
                		<td><?= $contrato[0]['descricao']?></td>
                		<td><?= preg_replace('/\s+.*$/', '', $contrato['ClienteProdutoContrato']['data_vigencia'])?></td>
                	</tr>
                <?php endforeach; ?>
        	</tbody>
        </table>
    </div>
<?php else: ?>
	<div class="alert">Nenhum registro encontrado.</div>
<?php endif; ?>