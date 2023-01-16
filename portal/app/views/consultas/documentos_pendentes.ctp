<?php if(count($lista_propostas)) : ?>

	<div class='well'>
    	<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array('controller' => $this->name, 'action' => $this->action, 'destino','export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
	</div>

	<table class="table table-striped">
	    <thead>
	        <tr>
	        	<th><strong>UF</strong></th>
	            <th><strong>Cidade</strong></th>
	            <th><strong>Razão Social</strong></th>
	            <th><strong>Telefone</strong></th>
	            <th><strong>E-mail</strong></th>
	            <th><strong>Documentação Pendente</strong></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach($lista_propostas as $proposta): ?>
		        <tr>
		        	<td><?php echo $proposta['PropostaCredEndereco']['estado'] ?></td>
		        	<td><?php echo $proposta['PropostaCredEndereco']['cidade'] ?></td>
		        	<td><?php echo $proposta['PropostaCredenciamento']['razao_social'] ?></td>
		        	<td><?php echo Comum::formatarTelefone($proposta['PropostaCredenciamento']['telefone']) ?></td>
		        	<td><?php echo $proposta['PropostaCredenciamento']['email'] ?></td>
		        	<td>
		        		- <?php echo implode("<br/ > - ", $proposta['DocumentosPendentes']); ?>
		        	</td>
		        </tr>
	        <?php endforeach; ?>        
	    </tbody>
	</table>
	<?php echo $this->Javascript->codeBlock(' $(function() { setup_mascaras(); }); '); ?>
<?php else : ?>
	<div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>


