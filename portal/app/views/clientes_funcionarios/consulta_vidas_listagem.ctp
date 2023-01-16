<?php if (!$dados): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php else: ?>	
	<div id="grafico_vidas_sintetico" class="row"></div>
	<table class="table table-striped">
	    <thead>
	        <tr>
				<td>Cliente</td>
				<td class='numeric input-small'>Vidas Ativas</td>
				<td class='numeric input-small'>Vidas Inativas</td>
				<td class='numeric input-small'>Total</td>
			</tr>
		</thead>
		<tbody>
			<?php			
			$total_ativo = 0;
			$total_inativo = 0;
			$total_funcionarios = 0;
			$topchart = 0;			
			$unidades = array();
			$series_ativo = array();
			$series_inativo = array();
			?>										
			<?php $series = array() ?>
			<?php foreach($dados as $key => $value) : ?>	
				<?php
				if($topchart < 5)
				{
					$unidades[] = "'".$value[0]['nome_fantasia']."'";
					$series_ativo[] =$value[0]['ativo'];
					$series_inativo[] =$value[0]['inativo'];					
					$topchart++;
				}
				?>
				<?php $codigo = empty($value['0']['codigo_cliente_alocacao']) ? -1 : $value['0']['codigo_cliente_alocacao'] ?>
				<?php				
				$codigo_cliente_alocacao = $value[0]['codigo_cliente_alocacao'];
				$codigo_cliente = $value[0]['codigo_cliente'];
				?>				
				<tr>
					<td><?php echo $value[0]['nome_fantasia']; ?></td>
					<td class='numeric input-small'>
					<?php
					if($value['0']['ativo'] == 0)
					{	
						echo $this->Buonny->moeda($value['0']['ativo'], array('nozero' => true));
					}else{						
						$total_ativo +=	$value[0]['ativo'];
	                    echo $html->link($value[0]['ativo'], array(
	                            'controller' => 'clientes_funcionarios', 
	                            'action' => 'consulta_vidas_analitico', 
	                            $codigo_cliente, 
	                            1,
	                            $codigo_cliente_alocacao
	                        ), 
	                        array('onclick' =>'return open_popup(this)', 'title' => 'Consulta Analitica'));
					}
					?>
					</td>
					<td class='numeric input-small'>
					<?php
					if($value['0']['inativo'] == 0)
					{	
						echo $this->Buonny->moeda($value['0']['inativo'], array('nozero' => true));
					}else{		
						$total_inativo +=	$value[0]['inativo'];			
	                    echo $html->link($value[0]['inativo'], array(
	                            'controller' => 'clientes_funcionarios', 
	                            'action' => 'consulta_vidas_analitico', 
	                            $codigo_cliente, 
	                            0,
	                            $codigo_cliente_alocacao
	                        ), 
	                        array('onclick' =>'return open_popup(this)', 'title' => 'Consulta Analitica'));					
					}
					?>					
					</td>
					<td class='numeric input-small'>
					<?php
					if($value[0]['total_funcionario'] == 0)
					{	
						echo $this->Buonny->moeda($value['0']['total_funcionario'], array('nozero' => true));
					}else{	
						$total_funcionarios +=	$value[0]['total_funcionario'];
	                    echo $html->link($value[0]['total_funcionario'], array(
	                            'controller' => 'clientes_funcionarios', 
	                            'action' => 'consulta_vidas_analitico', 
	                            $codigo_cliente,
	                            9,
	                            $codigo_cliente_alocacao
	                        ), 
	                        array('onclick' =>'return open_popup(this)', 'title' => 'Consulta Analitica'));					
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
				if($total_ativo == 0)
				{	
					echo $this->Buonny->moeda($total_ativo, array('nozero' => true));
				}else{						
                    echo $html->link($total_ativo, array(
                            'controller' => 'clientes_funcionarios', 
                            'action' => 'consulta_vidas_analitico', 
                            $codigo_total,
                            1,
                            $codigo_alocacao_total

                        ), 
                        array('onclick' =>'return open_popup(this)', 'title' => 'Consulta Analitica'));					
				}
				?>					
				</td>
				<td class='numeric input-small'>
				<?php
				if($total_inativo == 0)
				{	
					echo $this->Buonny->moeda($total_inativo, array('nozero' => true));
				}else{						
                    echo $html->link($total_inativo, array(
                            'controller' => 'clientes_funcionarios', 
                            'action' => 'consulta_vidas_analitico', 
                            $codigo_total,
                            0,
                            $codigo_alocacao_total

                        ), 
                        array('onclick' =>'return open_popup(this)', 'title' => 'Consulta Analitica'));					
				}
				?>				
				</td>
				<td class='numeric input-small'>
				<?php
				if($total_funcionarios == 0)
				{	
					echo $this->Buonny->moeda($total_funcionarios, array('nozero' => true));
				}else{						
                    echo $html->link($total_funcionarios, array(
                            'controller' => 'clientes_funcionarios', 
                            'action' => 'consulta_vidas_analitico', 
                            $codigo_total,
                            9,
                            $codigo_alocacao_total

                        ), 
                        array('onclick' =>'return open_popup(this)', 'title' => 'Consulta Analitica'));					
				}
				?>				
				</td>
			</tr>				
		</tfoot>
	</table>
	<?php $series[] = array('name' => '\'Ativo\'', 'values' => $series_ativo);?>
	<?php $series[] = array('name' => '\'Inativo\'', 'values' => $series_inativo);?>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render($unidades, $series, array(
	    'title' => false,
	    'renderTo' => 'grafico_vidas_sintetico',
	    'chart' => array('type' => 'column'),
	    'xAxis' => array('labels' => array('y' => 25, 'align' => 'center', 'style' => array('width' => '100', 'fontSize' => 10))),
	    'yAxis' => array('min' => '0', 'title' => 'Total'),
	    'legend' => array('align' => 'right', 'verticalAlign' => 'top', 'floating' => 'true', 'x' => -30, 'y' => 25, 'borderWidth' => 1),
	    'plotOptions' => array('series' => array('stacking' => 'normal', 'dataLabels' => array('enabled' => 'true'))),	    
	))); ?>	
<?php endif ?>