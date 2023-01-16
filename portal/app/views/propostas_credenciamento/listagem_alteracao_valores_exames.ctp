<?php echo $paginator->options(array('update' => 'div.lista')); ?>

<?php if(count($propostas_credenciamento)) : ?>
	<table class="table table-striped">
	    <thead>
	        <tr>
	        	<th><?php echo $this->Paginator->sort('Código', 'codigo'); ?></th>
	        	<th><?php echo $this->Paginator->sort('Data', 'data_inclusao'); ?></th>
	            <th><?php echo $this->Paginator->sort('Razão Social', 'razao_social'); ?></th>
	            <th><?php echo $this->Paginator->sort('Cidade / UF', 'cidade'); ?></th>
	            <th><?php echo $this->Paginator->sort('Status', 'status'); ?></th>
	            <th><?php echo $this->Paginator->sort('Responsável', 'usuario'); ?></th>
	           	<th style="text-align: right;">Opção</th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach($propostas_credenciamento as $proposta_credenciamento): ?>
		        <tr>
		        	<td><?php echo $proposta_credenciamento['PropostaCredenciamento']['codigo'] ?></td>
		        	<td style="width: 120px;"><?php echo $proposta_credenciamento['PropostaCredenciamento']['data_inclusao'] ?></td>
		        	<td><?php echo (trim($proposta_credenciamento['PropostaCredenciamento']['razao_social'])) ? $proposta_credenciamento['PropostaCredenciamento']['razao_social'] : $proposta_credenciamento['PropostaCredenciamento']['nome_fantasia'] ?></td>
		        	<td><?php echo $proposta_credenciamento[0]['cidade'] ?> / <?php echo $proposta_credenciamento[0]['estado'] ?></td>
		            <td>
		            	<?php echo $proposta_credenciamento['0']['status'] ?>
		            	<?php if(($proposta_credenciamento['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == '1') && ($proposta_credenciamento['PropostaCredenciamento']['ativo'] == '1')) : ?>
		            		( ATIVO )
		            	<?php endif; ?>
		            </td>
		            <td><?php echo (isset($proposta_credenciamento['Usuario']['nome']) && !empty($proposta_credenciamento['Usuario']['nome'])) ? $proposta_credenciamento['Usuario']['nome'] : '-' ?></td>
		            <td style="text-align: right;">
		                <?= $html->link('', array('action' => 'manutencao_valores_servicos', $proposta_credenciamento['PropostaCredenciamento']['codigo']), array('class' => 'icon-wrench', 'title' => 'Editar')) ?>
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
<?php else : ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>
<?php echo $this->Js->writeBuffer(); ?>