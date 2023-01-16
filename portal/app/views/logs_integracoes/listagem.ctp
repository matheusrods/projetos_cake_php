<?php if ($this->passedArgs[0] != 'export'): ?>
	<div class='well'>
	    <span class="pull-right">
	        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>   
	    </span>
	</div>
	<?php 
	    echo $this->Paginator->options(array('update' => 'div#logs_integracoes')); 
	?>
	<?php if(!empty($cliente)): ?>
		<div class="well">
			<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social']; ?>	
			<!-- <strong>Integradas: </strong><span class="text-success"><?php //$detalhes_integracao[0][0]['integrada']; ?></span> -->
			<!-- <strong>Não Integradas: </strong><span class="text-error"><?php //$detalhes_integracao[0][0]['nao_integrada']; ?></span> -->
			<?php if( !empty($cliente) && $cliente['Cliente']['codigo'] == '29610' ): ?>
				<?php if($this->Paginator->params['paging']['LogIntegracao']['count'] > 0): ?>
					<strong>Programações: </strong><?= $detalhes_integracao[0][0]['inclusao']; ?>
					<strong>Reprogramações: </strong><?= $detalhes_integracao[0][0]['alteracao']; ?>
					<strong>Cancelamentos: </strong><?= $detalhes_integracao[0][0]['cancelamento']; ?>
					<strong>Não Integrados: </strong><?= $detalhes_integracao[0][0]['nao_integrada']; ?>
					(<?php echo number_format($detalhes_integracao[0][0]['nao_integrada']*100/$this->Paginator->params['paging']['LogIntegracao']['count'],2) ?>%)
					<strong>Integrados: </strong><?= $detalhes_integracao[0][0]['integrada']; ?>
					(<?php echo number_format($detalhes_integracao[0][0]['integrada']*100/$this->Paginator->params['paging']['LogIntegracao']['count'],2) ?>%)
				<?php else: ?>
					<strong>Programações: </strong>0
					<strong>Reprogramações: </strong>0
					<strong>Cancelamentos: </strong>0
					<strong>Não Integrados: </strong>0
					(0.00%)
					<strong>Integrados: </strong>0
					(0.00%)
				<?php endif; ?>
			<?php endif; ?>	
		</div>
	<?php endif; ?>
	<table class='table table-striped'>
		<thead>
			<th class="input-mini">Cliente</th>
			<th class="input-mini">Pedido</th>
			<th class='input-medium'><?php echo $this->Paginator->sort('Data', 'data_inclusao') ?></th>
			<th class='input-medium'><?php echo $this->Paginator->sort('Arquivo', 'arquivo') ?></th>		
			<th>Descrição</th>
			<th>Origem</th>		
			<th>Operação</th>
			<th>Status</th>
			<th>Solicitante</th>
			<th class="input-mini">Placa</th>
			<th>Placa Alterada</th>
			<th>CPF</th>
			<th>CPF Alterado</th>
		</thead>
		<tbody>
			<?php $id_label=0; foreach ($logs_integracoes as $log_integracao): ?>			
				<tr>
					<td class='input-mini'><?= $log_integracao['LogIntegracao']['codigo_cliente'] ?></td>
					<td><?php echo $log_integracao['LogIntegracao']['numero_pedido'] ?></td>
					<td class='input-medium'><?= $log_integracao['LogIntegracao']['data_inclusao'] ?></td>
					<td class='input-medium'>
						<?php		
							$arquivo = trim($log_integracao['LogIntegracao']['arquivo']);
							if( empty($arquivo) || is_null($arquivo) )
								echo $this->Html->link('<i class="icon-eye-open"></i>', array('controller' => 'logs_integracoes', 'action' => 'view', $log_integracao['LogIntegracao']['codigo']), array('escape' => false, 'target' => '_blank'));
							else
								echo $this->Html->link($log_integracao['LogIntegracao']['arquivo'], array('controller' => 'logs_integracoes', 'action' => 'view', $log_integracao['LogIntegracao']['codigo']), array('target' => '_blank'));						
						?>
					</td>				
					<td>
						<?php 
							$id_label++;
							if( $log_integracao['LogIntegracao']['status'] || is_null($log_integracao['LogIntegracao']['status']) ){							
								echo '<strong>Erro: </strong>'. $log_integracao['LogIntegracao']['descricao'];
								
								if($log_integracao['LogIntegracao']['sistema_origem'] == 'SmGpa_FTP'){
									if( empty($log_integracao['LogIntegracao']['reprocessado']) ){
										echo '<div class="acao-'.$id_label.'">';
										echo '<a href="javascript:void(0);" onclick=\'reprocessar_arquivo_log_integracao("'.$log_integracao['LogIntegracao']['arquivo'].'",'.$id_label.','.$log_integracao['LogIntegracao']['codigo'].')\'"><span class="label label-important">Reprocessar Arquivo</span></a>';
										echo '</div>';
									}else{
										echo '<br /><strong>Reprocessado: </strong>'. $log_integracao['LogIntegracao']['reprocessado'];
									}
								}
							}else{
								//if( $log_integracao['LogIntegracao']['sistema_origem'] == 'SmGpa_FTP' || 
								//	$log_integracao['LogIntegracao']['sistema_origem'] == 'SM_Online' ||
								//	$log_integracao['LogIntegracao']['sistema_origem'] == 'SmAssai_WS')

								if(is_numeric($log_integracao['LogIntegracao']['descricao']))
									echo '<strong>SM: </strong>'. $this->Buonny->codigo_sm($log_integracao['LogIntegracao']['descricao']);
								else
									echo '<strong>Sucesso: </strong>'. $log_integracao['LogIntegracao']['descricao'];
							}
						?>
					</td>
					<td><?php echo $log_integracao['LogIntegracao']['sistema_origem']; ?></td>				
					<td>
						<?php 
							$tipo = $log_integracao['LogIntegracao']['tipo_operacao'];
							if($tipo == 'I') 
								echo 'INCLUSÃO';
							elseif($tipo == 'A') 
								echo 'ALTERAÇÃO';
							elseif($tipo == 'C')
								echo 'CANCELAMENTO';						
						?>
						</td>	
					<td><?php echo ( $log_integracao['LogIntegracao']['status'] ) ? 'NÃO INTEGRADA' : 'INTEGRADA';?></td>
					<td><?php echo ( $log_integracao['LogIntegracao']['solicitante'] );?></td>			
					<?php if($log_integracao['LogIntegracao']['tipo_operacao'] == 'A'): ?>
						<td class='input-mini'><?= $log_integracao[0]['placa_cavalo2'] ?></td>
						<td class='input-mini'><?= $log_integracao['LogIntegracao']['placa_cavalo'] ?></td>
					<?php else: ?>
						<td class='input-mini'><?= $log_integracao['LogIntegracao']['placa_cavalo'] ?></td>
						<td class='input-mini'><?= '' ?></td>
					<?php endif ?>
					<?php if($log_integracao['LogIntegracao']['tipo_operacao'] == 'A'): ?>
						<td class='input-mini'><?= $log_integracao[0]['cpf_motorista2'] ?></td>
						<td class='input-mini'><?= $log_integracao['LogIntegracao']['cpf_motorista'] ?></td>
					<?php else: ?>
						<td class='input-mini'><?= $log_integracao['LogIntegracao']['cpf_motorista'] ?></td>
						<td class='input-mini'><?= '' ?></td>
					<?php endif ?>



				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="13"><strong>Total:</strong> <?php echo $this->Paginator->params['paging']['LogIntegracao']['count']; ?></td>
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
	<?php echo $this->Js->writeBuffer(); ?>
<?php endif; ?>
