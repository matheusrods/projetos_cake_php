<?php if(count($propostas_credenciamento)) : ?>

	<?php echo $paginator->options(array('update' => 'div.lista')); ?>
	
	<table class="table table-striped">
	    <thead>
	        <tr>
	        	<th>Data Cadastro</th>
	        	<th>Credenciado</th>
	        	<th>Cidade</th>
	        	<th>UF</th>
	        	<th>Telefone</th>
	        	<th>E-mail</th>
	            <th>Responsável</th>
	            <th><?php echo $this->Paginator->sort('Status', 'status') ?></th>
	            <th>Aceite / Recusa</th>
	            <th>Motivo</th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach($propostas_credenciamento as $proposta_credenciamento) : ?>
		        <tr>
		        	<td><?php echo $proposta_credenciamento['PropostaCredenciamento']['data_inclusao'] ?></td>
		        	<td><?php echo (trim($proposta_credenciamento['PropostaCredenciamento']['razao_social'])) ? $proposta_credenciamento['PropostaCredenciamento']['razao_social'] : $proposta_credenciamento['PropostaCredenciamento']['nome_fantasia'] ?></td>
		        	<td><?php echo $proposta_credenciamento['PropostaCredEndereco']['cidade'] ?></td>
		        	<td><?php echo $proposta_credenciamento['PropostaCredEndereco']['estado'] ?></td>
		        	<td><?php echo Comum::formatarTelefone($proposta_credenciamento['PropostaCredenciamento']['telefone']) ?></td>
		        	<td><?php echo $proposta_credenciamento['PropostaCredenciamento']['email'] ?></td>
		        	<td>
		        		<?php if(isset($usuarios[$proposta_credenciamento['PropostaCredenciamento']['codigo_usuario_inclusao']])) : ?>
		        			<?php echo $usuarios[$proposta_credenciamento['PropostaCredenciamento']['codigo_usuario_inclusao']]; ?>
		        		<?php elseif(isset($usuarios[$proposta_credenciamento['PropostaCredenciamento']['codigo_usuario_alteracao']])) : ?>
		        			<?php echo $usuarios[$proposta_credenciamento['PropostaCredenciamento']['codigo_usuario_alteracao']]; ?>
		        		<?php else : ?>
		        			- 
		        		<?endif; ?>
		        	</td>
		        	<td><?php echo $proposta_credenciamento[0]['status'] ?></td>
		        	<td>
			        	<?php if(isset($proposta_credenciamento[0]['data_aprovado']) && !empty($proposta_credenciamento[0]['data_aprovado'])) : ?>
			        		<?php echo $proposta_credenciamento[0]['data_aprovado']; ?>
			        	<?php elseif(isset($proposta_credenciamento[0]['data_reprovado']) && !empty($proposta_credenciamento[0]['data_reprovado'])) : ?>
			        		<?php echo $proposta_credenciamento[0]['data_reprovado']; ?>
			        	<?php else : ?>
			        		-
			        	<?php endif; ?>
		        	</td>
		        	<td><?php echo isset($motivos_recusa[$proposta_credenciamento['PropostaCredenciamento']['codigo_motivo_recusa']]) ? $motivos_recusa[$proposta_credenciamento['PropostaCredenciamento']['codigo_motivo_recusa']] : ''; ?></td>
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
<?php else : ?>
	<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>