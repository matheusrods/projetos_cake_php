<ul class="nav nav-tabs">
	<li class="active"><a href="#gerais" data-toggle="tab" id="filial">Editar</a></li>
	<li><a href="#historico" data-toggle="tab" id="historico">Histórico</a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane active" id="gerais">
		<?php echo $this->BForm->create('ConfiguracaoComissao', array('type' => 'post','url' => array('controller' => 'configuracao_comissoes','action' => 'atualizar'))) ?>
		<?php echo $this->BForm->hidden('codigo'); ?>
		<?php echo $this->element('configuracao_comissoes/fields'); ?>
		<?php echo $this->BForm->end(); ?>
	</div>	

	<div class="tab-pane active"  id="logs" style="display:none">
		
		<div class='row-fluid inline'>
			<?php $cont = count($listagem);
				for( $i = 0; $i < $cont; $i++ ){
			  		$dados = $listagem[$i][0];
			  		$listagem[$i]['ConfiguracaoComissaoLog']= array_merge($listagem[$i]['ConfiguracaoComissaoLog'],$dados); 	
				}
			?>	
			
			<table class='table table-striped tablesorter' >
				<thead>
					<th class='input-mini'><?= $this->Html->link('Data Inclusão', 'javascript:void(0)') ?> 
						</th>
					<th class='input-medium'><?php 	echo $this->Html->link('Data Alteração','javascript:void(0)')?></th>
					<th class='input-medium'><?php 	echo $this->Html->link('Usuario Inclusão','javascript:void(0)')?></th>
					<th class='input-small'><?php 	echo $this->Html->link('Usuario Alterou','javascript:void(0)')?></th>
					<th class='input-small'><?php 	echo $this->Html->link('Ação','javascript:void(0)')?></th>
					<th class='input-medium'><?php 	echo $this->Html->link('Filial','javascript:void(0)')?></th>
					<th class='input-xlarge'><?php 	echo $this->Html->link('Produto','javascript:void(0)')?></th>
					<th class='input-small'><?php 	echo $this->Html->link('Tipo', 'javascript:void(0)') ?></th>
					<th class='input-numeric'><?php echo $this->Html->link('Comissão','javascript:void(0)')?></th>
				</thead>
				<tbody>	
					<?php $n= 0;?> 			
					<?php foreach ($listagem as $obj):?>
						<tr>
							<td><?php echo $obj['ConfiguracaoComissaoLog']['data_inclusao']?></td>
		                    <td><?php echo $obj['ConfiguracaoComissaoLog']['data_alteracao']?></td>
		                    <td><?php echo $obj['UsuarioInclusao']['apelido']?></td>
		                    <td><?php echo $obj['UsuarioAlteracao']['apelido']?></td>
		                    <td><?php echo $obj['ConfiguracaoComissaoLog']['acao']?></td>
		                    <td><?php echo $obj['EnderecoRegiao']['descricao']?></td>
		                    <td><?php echo $obj['NProduto']['descricao']?></td>
		                    <td><?php echo $obj['ConfiguracaoComissaoLog']['Faturamento']?></td>
		                    <td><?php echo $obj['ConfiguracaoComissaoLog']['Comissao'].'%'?></td>
						</tr>
						<?php $n++; ?>
					<?php endforeach;?>
				</tbody>
				
				<tfoot>
					<tr>
						<td colspan = "9"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ConfiguracaoComissaoLog']['count']; ?></td>
					</tr>
				</tfoot>
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
		</div>	
		<?php echo $this->Js->writeBuffer(); ?>	
	</div>
</div>
<?php echo $this->Buonny->link_css('tablesorter') ?>
<?php echo $this->Buonny->link_js('jquery.tablesorter.min') ?>
<?php echo $this->Javascript->codeBlock("jQuery('table.table').tablesorter()") ?>
<?php echo $this->Javascript->codeBlock('
	    jQuery(document).ready(function(){
			$("#historico").click(function(){
				$("#logs").show();
				$("#gerais").hide();
			});
			$("#filial").click(function(){
				$("#gerais").show();
				$("#logs").hide();
			});
			

	    });', false);
	?>

