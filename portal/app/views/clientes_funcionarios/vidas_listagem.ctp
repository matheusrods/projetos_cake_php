<?php if (!$dados): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php else: ?>	
	
	<div id="grafico_vidas" style="margin:  0 auto;"></div>
	
	<table class="table table-striped">
	    <thead>
	        <tr>
				<td>Razão Social</td>
				<td class='numeric input-small'>Vidas Ativas</td>
				<td class='numeric input-small'>Vidas Inativas</td>
				<td class='numeric input-small'>Total</td>
			</tr>
		</thead>
		<tbody>
			<?php			
			$total_ativo = 0;
			$total_inativo = 0;			
			$total_geral = 0;
			?>										
			<?php $series = array() ?>
			<?php foreach($dados as $key => $value) : ?>	
				<?php
				$codigo_cliente = $value[0]['codigo_cliente'];
				?>
				<?php $series[] = array('name' => '"'.str_replace('"', "'", $this->Buonny->leiamais($value[0]['razao_social'], 64 ,$mais = '.. ') ).'"', 'values' => $value[0]['total_geral']);?>
				<tr>
					<td><?php echo $value[0]['razao_social']; ?></td>
					<td class='numeric input-small'>
					<?php
					if($value['0']['total_ativo'] == 0)
					{	
						echo $this->Buonny->moeda($value['0']['total_ativo'], array('nozero' => true));
					}else{						
						$total_ativo +=	$value[0]['total_ativo'];
	                    echo $html->link($value[0]['total_ativo'], array(
	                            'controller' => 'clientes_funcionarios', 
	                            'action' => 'consulta_vidas', 
	                            $codigo_cliente, 
	                            1
	                        ));
					}
					?>
					</td>
					<td class='numeric input-small'>
					<?php
					if($value['0']['total_inativo'] == 0)
					{	
						echo $this->Buonny->moeda($value['0']['total_inativo'], array('nozero' => true));
					}else{		
						$total_inativo +=	$value[0]['total_inativo'];			
	                    echo $html->link($value[0]['total_inativo'], array(
	                            'controller' => 'clientes_funcionarios', 
	                            'action' => 'consulta_vidas', 
	                            $codigo_cliente, 
	                            0
	                        ));					
					}
					?>					
					</td>
					<td class='numeric input-small'>
					<?php
					if($value[0]['total_geral'] == 0)
					{	
						echo $this->Buonny->moeda($value['0']['total_geral'], array('nozero' => true));
					}else{	
						$total_geral +=	$value[0]['total_geral'];
	                    echo $html->link($value[0]['total_geral'], array(
	                            'controller' => 'clientes_funcionarios', 
	                            'action' => 'consulta_vidas', 
	                            $codigo_cliente,
	                            9
	                        ));					
					}
					?>	
					</td>			
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>		
	        <tr>
				<td>Total</td>
				<td class='numeric input-small'>
				<?php
					if($total_ativo > 0)
					{
						echo $total_ativo;
					}
				?>					
				</td>
				<td class='numeric input-small'>
				<?php
					if($total_inativo > 0)
					{
						echo $total_inativo;
					}
				?>				
				</td>
				<td class='numeric input-small'>
				<?php
					if($total_geral > 0)
					{
						echo $total_geral;
					}
				?>				
				</td>
			</tr>				
		</tfoot>
	</table>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render(array(), $series, array(
	    'title' => '',
	    'renderTo' => 'grafico_vidas',
	    'chart' => array('type' => 'pie'),
	    'xAxis' => array('labels' => array('style' => array('width' => '100', 'fontSize' => 10))),
	    'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.x; }'),
	    'plotOptions' => array('pie' => array('showInLegend'=>true)),
	    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false'))),
	    'legend' => array('enabled' => 'false')
	))); ?>	
<?php endif ?>