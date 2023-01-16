
<div class="lista">	
	<div class="page-title">
		<h3>Veículo Sintético Checklist</h3>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->Paginator->options(array('update' => '.lista')); ?>
		<?php //if(isset($Cliente) && $Cliente!=NULL || $dados != 'total'):  ?>
		<?php if(isset($Cliente) && $Cliente!=NULL && !isset($dados)):  ?>	
			<div class="well">
				<strong>Código:</strong> <?php echo $Cliente['Cliente']['codigo'] ?>
			    <strong>Cliente: </strong><?php echo $Cliente['Cliente']['razao_social']?>
			</div>
		<?php endif; ?>
		<table class="table table-striped table-bordered">
		    <thead>
		        <tr>
		            <th class='input-mini'>
		            	<?php //echo $this->Paginator->sort('Placa','veic_placa')?>
		            	<?php echo $this->Paginator->sort('Placa','TVeicVeiculo.veic_placa')?>
		            </th>
		            
		           	<?php if($group=='transportador' || $group=='operador' || $group == 'CD'): ?> 
		            	<th class= <?php echo $group == 'transportador' ?'input-xlarge':'input-mini'?>>
		            		<?php if( $group == 'CD'){
		            				echo $this->Paginator->sort(ucfirst('transportador'),'TPjurPessoaJuridica.pjur_razao_social'); 
		            			}else{
		            				echo $this->Paginator->sort(ucfirst($group),$group != 'transportador' ?'pjur_razao_social':'cvei_usuario_adicionou' ); 
		               			}
		               		?>		 

		               	</th>
		            <?php elseif($group=='placa'|| $group == 'AprovadoReprovado'):?>   	
		               	<th class= <?php echo $group == 'placa' || $group == 'AprovadoReprovado' ?'input-xlarge':'input-mini'?>>
		            		<?php echo $this->Paginator->sort('Transportador','pjur_razao_social');?>
		               	</th>
		            <?php endif?>

		            <?php if($group=='transportador' || $group=='placa' || $group == 'AprovadoReprovado' || $group == 'CD'): ?>
		            	<?php if( $group == 'CD'):?>
		            		<th class='input-mini'><?php echo $this->Paginator->sort('Operador','TCveiChecklistVeiculo.cvei_usuario_adicionou')?>
		           		<?php else:?>
		           			<th class='input-mini'><?php echo $this->Paginator->sort('Operador','cvei_usuario_adicionou')?>
		           		<?php endif?>	
		           	<?php endif?>

		            <?php if( $group == 'CD'):?>
		            		<th class='input-mini'><?php echo $this->Paginator->sort('Data Checklist','TCveiChecklistVeiculo.cvei_data_cadastro')?>
		           			<th class='input-mini'><?php echo $this->Paginator->sort('Status','TCveiChecklistVeiculo.cvei_status')?></th>
		           		<?php else:?>
		           			<th class='input-mini'><?php echo $this->Paginator->sort('Data Checklist','cvei_data_cadastro')?></th>
		            		<th class='input-mini'><?php echo $this->Paginator->sort('Status','status')?></th>
		            <?php endif?>
		            

		        

		        </tr>
		    </thead>
		    <tbody>
		    	
		       	<?php  $i=0;?> 
		        <?php foreach ($listagem as $lista): ?>
		            <?php if(isset($dados)&& $dados == 'total'):?>
		            	<?php  $codigo_cliente = $Cliente[$i]['Cliente']['codigo'];?>
		        	
		        	<?php else:?>
			        	<?php 
			            	if(isset($group) && $group =='transportador'){
			               	 	$codigo_cliente = $Cliente['Cliente']['codigo'];
			               	}elseif (isset($group)&& $group =='AprovadoReprovado'){
			               		$codigo_cliente = $Cliente['Cliente']['codigo'];
			               	}elseif(isset($group) && $group == 'operador'){ 
			                 	$codigo_cliente = $ClienteOperador['Cliente']['codigo'];
			                }elseif (isset($group) && $group == 'placa') {
			                	$codigo_cliente = $PlacaCliente['Cliente']['codigo'];
			                }elseif (isset($group) && $group == 'data') {
			                	$codigo_cliente = $codigo[$i]['Cliente']['codigo'];
			                }elseif (isset($group) && $group == 'CD') {
			                	$codigo_cliente = $codigo[$i]['Cliente']['codigo'];
			                }

		            	?>
		            <?php endif?>	
	            	<?php $VizualizaChecklistPlaca = array('controller' => 'veiculos', 'action' => 'visualizar_checklist', 'VeiculoSinteticoChecklist','cvei_codigo'=>$lista['TCveiChecklistVeiculo']['cvei_codigo'],'veic_placa'=>$lista['TVeicVeiculo']['veic_placa'],'codigo_cliente'=>$codigo_cliente); ?>
	            	     	

		            <tr>
		                <td class='input-mini'>
		                	<?php echo  $this->Buonny->placa(Comum::formatarPlaca($lista['TVeicVeiculo']['veic_placa']),date('d/m/Y 00:00:00'), date('d/m/Y 23:59:59'));?>
			            </td>
		                	<?php if($group=='transportador' || $group=='operador'|| $group == 'AprovadoReprovado' || $group == 'CD' ):?>
				                <td class= <?php echo $group == 'transportador' || $group == 'AprovadoReprovado' || $group == 'CD' ?'input-xxlarge':'input-mini'?>>
				                   	<?php echo $group == 'transportador' || $group == 'AprovadoReprovado' || $group == 'CD' ? $lista['TPjurPessoaJuridica']['pjur_razao_social'] :$lista['TCveiChecklistVeiculo']['cvei_usuario_adicionou']?> 	
				                </td>
				            <?php elseif($group=='placa'):?>
				            	<td class= <?php echo $group == 'placa'?'input-xxlarge':'input-mini'?>><?php echo $lista['TPjurPessoaJuridica']['pjur_razao_social'];?>
				            	</td>				                
				            <?php endif?>    
		                
		                <?php if($group=='transportador'|| $group=='placa' || $group == 'AprovadoReprovado' || $group == 'CD'): ?>
		                	<td class='input-small'>	
		                		<?php echo $lista['TCveiChecklistVeiculo']['cvei_usuario_adicionou']?> 	
		                	</td>
		                <?php endif?>	
		                <td class=<?php echo $group == 'transportador' || 'placa' ?'input-medium':'input-mini'?>><?php  echo $lista['TCveiChecklistVeiculo']['cvei_data_cadastro']?>
		                </td>
		                <td><?php echo $this->Html->link($lista['TCveiChecklistVeiculo']['status'],$VizualizaChecklistPlaca,array('onclick'=>"return open_popup(this);",'title'=>'Visualizar Checklist'))?></td>
		                
		            </tr>
		            <?php  $i++;?> 
		        <?php endforeach; ?>        
		    </tbody>
		    <tfoot>
	            <tr>
	                <td class='input-mini'><strong>Total</strong></td>
	                <td class="numeric"><strong><?php echo $totalPlacaSinteticoCheclist[0][0]['total'] ?></strong></td>
	                <td class=<?php echo $group == 'transportador' ?'input-small':'input-mini'?>></td>
	                <?php if( $group =='transportador' || $group =='placa' || $group == 'AprovadoReprovado' || $group == 'CD'): ?>
	                	<td class="input-small"></td>
	                	<td class=<?php echo $group == 'placa' ?'input-small':'input-mini'?>></td>
	                <?php endif?>
	                <?php if($group =='operador' ): ?>
	                	<td></td>
	                <?php endif?>
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
	</div>
</div>	
<?php echo $this->Js->writeBuffer(); ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
