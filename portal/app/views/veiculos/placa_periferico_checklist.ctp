<div class="lista">	
	<div class="page-title">
		<h3>Veículos Analítico Periféricos</h3>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->Paginator->options(array('update' => '.lista')); ?>
		<table class="table table-striped table-bordered">
		    <thead>
		        <tr>
		            <th class='input-small'><?php echo $this->Paginator->sort('Placa','veic_placa')?></th>
		            <th class='input-mini'><?php echo $this->Paginator->sort('Operador','cvei_usuario_adicionou')?></th>    
		            <th class='input-mini'><?php echo $this->Paginator->sort('Data Checklist','cvei_data_cadastro')?></th>
		            <th class='input-mini'><?php echo $this->Paginator->sort('Status','status')?></th>
		        </tr>
		    </thead>
		    <tbody>
		    	
		       	<?php  $i=0;?> 
		        <?php foreach ($listagem as $lista): ?>
		            	
	            	<?php $VizualizaChecklistPlaca = array('controller' => 'veiculos', 'action' => 'visualizar_checklist', 'VeiculoSinteticoChecklist','cvei_codigo'=>$lista['TCveiChecklistVeiculo']['cvei_codigo'],'veic_placa'=>$lista['TVeicVeiculo']['veic_placa'],'codigo_cliente'=>$Cliente[$i]['Cliente']['codigo']); 
	            		array_push($VizualizaChecklistPlaca,$Problemas);
	            		if($Problemas!='total'){
	            			$VizualizaChecklistPlaca['Problemas'] = $VizualizaChecklistPlaca[1];
	            		}else{	
	            			$VizualizaChecklistPlaca['Problemas'] = $lista['TPpadPerifericoPadrao']['ppad_codigo'];
	            		}
	            		 
	            		unset($VizualizaChecklistPlaca[1]);
	            	?>		
	            	
		            <tr>
		                <td class='input-mini'>
		                	<?php echo  $this->Buonny->placa(Comum::formatarPlaca($lista['TVeicVeiculo']['veic_placa']),date('d/m/Y 00:00:00'), date('d/m/Y 23:59:59'),$Cliente[$i]['Cliente']['codigo']);?>
			            </td>
				        <td class='input-small'><?php echo $lista['TCveiChecklistVeiculo']['cvei_usuario_adicionou']?></td>
		                <td class='input-large'>
		                	<?php  echo $this->Html->link($lista['TCveiChecklistVeiculo']['cvei_data_cadastro'],$VizualizaChecklistPlaca,array('onclick'=>"return open_popup(this);",'title'=>'Visualizar Checklist'))?>
		                </td>
		                <td class='input-small'><?php echo $lista['TIcveItemChecklistVeiculo']['status']?></td>
		            </tr>
		            <?php  $i++;?> 
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
		<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
	</div>
</div>	

