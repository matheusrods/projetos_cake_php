<?php if(isset($listagem)&& !empty($listagem)):?>
	<div class='row-fluid'>	
		<div id="grafico"  style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
			<?php 	
				$nome = array();
				for ($i = 0, $count = count($listagem); $i < $count; $i++) {  	
					$eixo_x[$i] =  "'".$listagem[$i]['TPpadPerifericoPadrao']['ppad_descricao']."'";				    	
					$perifericos = $listagem[$i]['TIcveItemChecklistVeiculo']['aprovados']+ $listagem[$i]['TIcveItemChecklistVeiculo']['reprovados'];
				    $series[$i]  =array('name' => $eixo_x[$i],'values'=>$perifericos);
				}
			 	
			 	echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
					'renderTo' => 'grafico',
					'chart' => array('type' => 'pie'),
					'yAxis' => array('title' => 'Periféricos'),
					'xAxis' => array('labels' => array('rotation' => -70, 'y' => 20), 'gridLineWidth' => 1),
					'plotOptions' => array('pie' => array('showInLegend' => 'true')),
					'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'true'), 'printButton' => array('enabled'=> 'true')))
	    		)));
			?>	
		</div>	
	</div>	
<?php endif; ?>
<?php if(isset($codigo_cliente) && $codigo_cliente!=NULL && $listagem != NULL ):  ?>
	<div class="well">
		<strong>Código: </strong><?= $codigo_cliente ?>
	    <strong>Cliente: </strong><?= $razao_social ?>
	</div>
<?php endif; ?>
<?php if(!empty($listagem)):?>
<div class='row-fluid inline' style="min-height: 800px">
	<?php echo $this->Paginator->options(array('update' => '.lista')); ?>
	<table class="table table-striped table-bordered">
	    <thead>
	        <tr>	            
	            <th><strong><?php echo $this->Paginator->sort('Periféricos','ppad_codigo')?></strong></th>
	            <th class='numeric'><?php echo $this->Paginator->sort('Aprovados','aprovados')?></th>
	            <th class='numeric'><?php echo $this->Paginator->sort('Reprovados','reprovados')?></th>
	            <th class='numeric'><?php echo $this->Paginator->sort('Total')?></th>
	        </tr>
	    </thead>
		    <?php
		    	$dados = NULL ;
		    	$total_aprovados = NULL;
		    	$total_reprovados = NULL;
		    ?>		    
		    <?php foreach ($listagem as $lista): ?>		    	
		    	<tbody>	        
		    		<?php $total_aprovados += $lista['TIcveItemChecklistVeiculo']['aprovados'];?>
		    		<?php $total_reprovados += $lista['TIcveItemChecklistVeiculo']['reprovados'];?>
		            <?php $dados = $lista['TPpadPerifericoPadrao']['ppad_codigo'];?>	
		            <tr>
		                <td class ="input-xlarge"?>
		                	<?= $lista['TPpadPerifericoPadrao']['ppad_descricao']; ?> 
		                </td>
		                <td class='numeric'>
		                    <?= $this->Html->link($this->Buonny->moeda($lista['TIcveItemChecklistVeiculo']['aprovados'], array('nozero' => true, 'places' => 0)
		                    ),array('controller'=>'Veiculos', 'action'=>'placa_periferico_checklist',$status = '1',$dados,'Problema'),array('onclick'=>"return open_popup(this);",'title'=>'Aprovados'))?>
		                </td>
		                <td class='numeric'>
		                    <?=  $this->Html->link($this->Buonny->moeda($lista['TIcveItemChecklistVeiculo']['reprovados'], array('nozero' => true, 'places' => 0)
		                    ),array('controller'=>'Veiculos', 'action'=>'placa_periferico_checklist',$status = '0',$dados,'Problema'),array('onclick'=>"return open_popup(this);",'title'=>'Reprovados'))?>
		                </td class='numeric'>
		                <td class='numeric'>
		                	<?=  $this->Html->link($this->Buonny->moeda($lista['TIcveItemChecklistVeiculo']['reprovados']+ $lista['TIcveItemChecklistVeiculo']['aprovados'], array('nozero' => true, 'places' => 0)
		                    ),array('controller'=>'Veiculos', 'action'=>'placa_periferico_checklist',$status = 'total',$dados,'Problema'),array('onclick'=>"return open_popup(this);",'title'=>'Total'))?>              	 
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
                <td class="numeric"><strong><?= $this->Html->link($this->Buonny->moeda($total_aprovados, array('nozero' => true, 'places' => 0)),array('controller'=>'Veiculos', 'action'=>'placa_periferico_checklist',$status = '1',$dados,'Problema'),array('onclick'=>"return open_popup(this);",'title'=>'Total Aprovados')) ?></strong></td>
                <td class="numeric"><strong><?=  $this->Html->link($this->Buonny->moeda($total_reprovados, array('nozero' => true, 'places' => 0)),array('controller'=>'Veiculos', 'action'=>'placa_periferico_checklist',$status = '0',$dados,'Problema'),array('onclick'=>"return open_popup(this);",'title'=>'Total Reprovados')) ?></strong></td>
                <td class="numeric"><strong><?= $this->Html->link($this->Buonny->moeda(($total_reprovados + $total_aprovados), array('nozero' => true, 'places' => 0)),array('controller'=>'Veiculos', 'action'=>'placa_periferico_checklist',$status = 'total',$dados,'Problema'),array('onclick'=>"return open_popup(this);",'title'=>'Total'))?></strong></td>
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
<?php else:?>
	<div class="alert">Nenhum Registro Encontrado</div>
<?php endif;?>