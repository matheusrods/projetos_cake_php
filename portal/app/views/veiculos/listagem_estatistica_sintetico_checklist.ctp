<?php if(isset($listagem)&& !empty($listagem)):?>
	<div class='row-fluid'>	
		<div id="grafico"  style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
			<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
			<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
		
			<?php 	
				$nome = array();
				for ($i = 0, $count = count($listagem); $i < $count; $i++) {  	
					if(isset($group) && $group =='transportador'){
				         $eixo_x[$i] =  "'".$listagem[$i]['TPjurPessoaJuridica']['pjur_razao_social']."'"; 
				    
				    }elseif(isset($group) && $group == 'operador'){ 
				    	$eixo_x[$i] = "'".$listagem[$i]['TCveiChecklistVeiculo']['cvei_usuario_adicionou']."'";
				    
				    }elseif(isset($group) && $group == 'placa'){ 
				    	$eixo_x[$i] =  "'".$listagem[$i]['TVeicVeiculo']['veic_placa']."'";
				    
				    }elseif (isset($group) && $group == 'data'){
				    	$eixo_x[$i] =  "'".$listagem[$i][0]['data']."'";
  				    
  				    }elseif (isset($group) && $group == 'CD') {
  				    	$eixo_x[$i] =  "'".$listagem[$i]['TRefeReferencia']['refe_descricao']."'";	
  				    
				    }elseif (isset($group) && $group == 'Aprovado/Reprovado') {
				       	$eixo_x[0]  =  "'".$listagem[$i]['TCveiChecklistVeiculo']['aprovados']."'";
				    	$eixo_x[1]  =  "'".$listagem[$i]['TCveiChecklistVeiculo']['reprovados']."'";
				       	$Aprovados  = 	   $listagem[$i]['TCveiChecklistVeiculo']['aprovados'];
				    	$Reprovados =      $listagem[$i]['TCveiChecklistVeiculo']['reprovados'];

				    	$series['Reprovados'] = array('name' =>"'Reprovados'",'values'=>$Reprovados);
				    	$series['Aprovados'] = array('name' =>"'Aprovados'",'values'=>$Aprovados);
				   
				    }
				    
				    if(isset($group) && $group != 'Aprovado/Reprovado'){
				       	$Checklist = $listagem[$i]['TCveiChecklistVeiculo']['aprovados']+ $listagem[$i]['TCveiChecklistVeiculo']['reprovados'];
				    	$series[$i]  =array('name' => $eixo_x[$i],'values'=>$Checklist);
				    }
			   	    				    			   	    
				    $Reprovados[$i] = $listagem[$i]['TCveiChecklistVeiculo']['reprovados'];
			    }   
			   										
				echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
					'renderTo' => 'grafico',
					'chart' => array('type' => 'pie'),
					'yAxis' => array('title' => ucfirst($group)),
					'xAxis' => array('labels' => array('rotation' => -70, 'y' => 20), 'gridLineWidth' => 1),
					'plotOptions' => array('pie' => array('showInLegend' => 'true')),
					'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'true'), 'printButton' => array('enabled'=> 'true')))
	    		)));
			?>	
		</div>	
	</div>	
<?php endif; ?>
<div><br/><br/></div>
 

<div class='row-fluid inline' style="min-height: 800px">
	<?php echo $this->Paginator->options(array('update' => '.lista')); ?>
	<?php if($group =='transportador' && $codigo_cliente!=NULL && $listagem != NULL ):  ?>
		<div class="well">
			<strong>Código: </strong><?= $codigo_cliente ?>
		    <strong>Cliente: </strong><?= $listagem[0]['TPjurPessoaJuridica']['pjur_razao_social'] ?>
		</div>
	<?php endif; ?>

	<table class="table table-striped table-bordered">
	    <thead>
	        <tr>
	            
	            <th>
	            	<strong>
	            		<?php
	            			if($group == 'CD'){
	            				echo $this->Paginator->sort(ucfirst($group),$group);
	            			}else{
	             				echo $this->Paginator->sort(ucfirst($group),'TPjurPessoaJuridica.pjur_razao_social');
	             			}
	             		?>
	             	</strong>
	            </th>
	            <th class='numeric'><?php echo $this->Paginator->sort('Aprovados','aprovados')?></th>
	            <th class='numeric'><?php echo $this->Paginator->sort('Reprovados','reprovados')?></th>
	            <th class='numeric'><?php echo $this->Paginator->sort('Total','total')?></th>

	        </tr>
	    </thead>
		    <?php  $dados = NULL ?>
		    <?php foreach ($listagem as $lista): ?>
		    	
		    	<tbody>
		        
		        
		            <?php 
		            	if(isset($group) && $group =='transportador'){
		               	 	$dados = $lista['TCveiChecklistVeiculo']['cvei_pess_oras_codigo']; 
		             	}elseif(isset($group) && $group == 'Aprovado/Reprovado'){ 
		             		$dados='total';
		             	}	
		             	elseif(isset($group) && $group == 'operador'){ 
		                    $dados = $lista['TCveiChecklistVeiculo']['cvei_usuario_adicionou'];
		                }elseif(isset($group) && $group == 'placa'){ 
		                    $dados = $lista['TVeicVeiculo']['veic_placa'];
		                }elseif (isset($group) && $group == 'data'){
		                	$dados = AppModel::dateToDbDate2($lista[0]['data']);
		                }elseif (isset($group) && $group == 'CD') {
		                	 $dados = $lista['TRefeReferencia']['refe_descricao'];
		                }
		                 

		            ?>	

		            <tr>
		                <td class=<?php echo $group == 'data' ?'input-medium':''?>>
		                	<?php 
	                            if($group == 'transportador')  
	                             	echo $lista['TPjurPessoaJuridica']['pjur_razao_social'];
	                            if($group == 'placa') 
	                               	echo  Comum::formatarPlaca($lista['TVeicVeiculo']['veic_placa']);
	                            if($group=='operador')   
		                        	echo $lista['TCveiChecklistVeiculo']['cvei_usuario_adicionou'];
		                     	if($group=='data')
		                     		echo $lista[0]['data'];
		                     	if($group=='Aprovado/Reprovado'){
		                     		echo 'Aprovado e Reprovado';
		                     		$group = 'AprovadoReprovado';
		                     	}
		                     	if($group=='CD'){
		                     		echo $lista['TRefeReferencia']['refe_descricao'];
		                     		//unset($lista['type']);
		                     	}

		                    ?> 
		                </td>
		                <td class='numeric'>
		                    <?= $this->Html->link($this->Buonny->moeda($lista['TCveiChecklistVeiculo']['aprovados'], array('nozero' => true, 'places' => 0)
		                    ),array('controller'=>'Veiculos', 'action'=>'lista_placa_sintetico_checklist',$status = '1',$dados,$group),array('onclick'=>"return open_popup(this);",'title'=>'Aprovados'))?>
		                </td>
		                <td class='numeric'>
		                    <?=  $this->Html->link($this->Buonny->moeda($lista['TCveiChecklistVeiculo']['reprovados'], array('nozero' => true, 'places' => 0)
		                    ),array('controller'=>'Veiculos', 'action'=>'lista_placa_sintetico_checklist',$status = '0',$dados,$group),array('onclick'=>"return open_popup(this);",'title'=>'Reprovados'))?>
		                </td class='numeric'>
		                <td class='numeric'>
		                	<?=  $this->Html->link($this->Buonny->moeda($lista['TCveiChecklistVeiculo']['reprovados']+ $lista['TCveiChecklistVeiculo']['aprovados'], array('nozero' => true, 'places' => 0)
		                    ),array('controller'=>'Veiculos', 'action'=>'lista_placa_sintetico_checklist',$status = 'total',$dados,$group),array('onclick'=>"return open_popup(this);",'title'=>'Total'))?>              	 
		                </td>
		            </tr>
		        </tbody>
		    <?php endforeach; ?> 
	    <tfoot>
            <tr>
                <td><strong>Total</strong></td>
                <?php if(isset($cliente_postgres) && $cliente_postgres != NULL): ?>
                	<?php $dados = $cliente_postgres?>                	
                <?php else:?>
                    <?php $dados = 'total';?>	
                <?php endif ?>	
                                
                <td class="numeric"><strong><?= $this->Html->link($this->Buonny->moeda($totalChecklist[0]['TCveiChecklistVeiculo']['aprovados'], array('nozero' => true, 'places' => 0)),array('controller'=>'Veiculos', 'action'=>'lista_placa_sintetico_checklist',$status = '1',$dados,$group),array('onclick'=>"return open_popup(this);",'title'=>'Total Aprovados')) ?></strong></td>
                
                <td class="numeric"><strong><?=  $this->Html->link($this->Buonny->moeda($totalChecklist[0]['TCveiChecklistVeiculo']['reprovados'], array('nozero' => true, 'places' => 0)),array('controller'=>'Veiculos', 'action'=>'lista_placa_sintetico_checklist',$status = '0',$dados,$group),array('onclick'=>"return open_popup(this);",'title'=>'Total Reprovados')) ?></strong></td>
                
                <td class="numeric"><strong><?= $this->Html->link($this->Buonny->moeda($totalChecklist[0]['TCveiChecklistVeiculo']['aprovados'] + $totalChecklist[0]['TCveiChecklistVeiculo']['reprovados'], array('nozero' => true, 'places' => 0)),array('controller'=>'Veiculos', 'action'=>'lista_placa_sintetico_checklist',$status = 'total',$dados,$group),array('onclick'=>"return open_popup(this);",'title'=>'Total'))?></strong></td>
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
