<?php if(isset($estatistica) && count($estatistica) > 0): ?>
    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
	<table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Meses</th>
                    <th>Duração 1 dia</th>
                    <th>Duração 2 dias</th>
                    <th>Duração 3 dias</th>
                    <th>Duração 4 dias</th>
                    <th>Duração + 4 dias</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                    $numero_mes        = 1;
                    $total_umdia       = 0;
                    $total_doisdias    = 0;
                    $total_tresdias    = 0;
                    $total_quatrodias  = 0;
                    $total_maisdquatro = 0;
                    $total_meses       = 0;
                    foreach($estatistica as $valor):
                ?>
                <tr>
                    <td><?php echo $this->Buonny->mes_extenso($numero_mes); ?></td>
                    <?php $valor1 = isset($valor[1]) ? $valor[1] : 0 ?>
                    <?php $valor2 = isset($valor[2]) ? $valor[2] : 0 ?>
                    <?php $valor3 = isset($valor[3]) ? $valor[3] : 0 ?>
                    <?php $valor4 = isset($valor[4]) ? $valor[4] : 0 ?>
                    <?php $valor5 = isset($valor[5]) ? $valor[5] : 0 ?>
                    <?php $total_mes = $valor1 + $valor2 + $valor3 + $valor4 + $valor5 ?>
                    <?php $total_umdia += $valor1 ?>
                    <?php $total_doisdias += $valor2 ?>
                    <?php $total_tresdias += $valor3 ?>
                    <?php $total_quatrodias += $valor4 ?>
                    <?php $total_maisdquatro += $valor5 ?>
                    <?php $total_meses += $total_mes ?>
                    <td class="numeric"><?php echo $valor1 ?></td>
                    <td class="numeric"><?php echo $valor2 ?></td>
                    <td class="numeric"><?php echo $valor3 ?></td>
                    <td class="numeric"><?php echo $valor4 ?></td>
                    <td class="numeric"><?php echo $valor5 ?> </td>
                    <td class="numeric"><?php echo $total_mes ?></td>
                </tr>
                <?php $numero_mes++; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Média dos Meses</strong></td>
                    <td class="numeric"><?php echo $total_umdia != 0 ? (int)($total_umdia / count($estatistica)): ''; ?></td>
                    <td class="numeric"><?php echo $total_doisdias != 0 ? (int)($total_doisdias / count($estatistica)): ''; ?></td>
                    <td class="numeric"><?php echo $total_tresdias != 0 ? (int)($total_tresdias / count($estatistica)): ''; ?></td>
                    <td class="numeric"><?php echo $total_quatrodias != 0 ? (int)($total_quatrodias / count($estatistica)): ''; ?></td>
                    <td class="numeric"><?php echo $total_maisdquatro != 0 ? (int)($total_maisdquatro / count($estatistica)): ''; ?></td>
                    <td class="numeric"><?php echo $total_maisdquatro != 0 ? (int)($total_meses / count($estatistica)): ''; ?></td>
                </tr>
                <tr>
                    <td><strong>Total Geral</strong></td>
                    <td class="numeric"><?php echo $total_umdia != 0 ? $total_umdia: ''; ?></td>
                    <td class="numeric"><?php echo $total_doisdias != 0 ? $total_doisdias: ''; ?></td>
                    <td class="numeric"><?php echo $total_tresdias != 0 ? $total_tresdias: ''; ?></td>
                    <td class="numeric"><?php echo $total_quatrodias != 0 ? $total_quatrodias: ''; ?></td>
                    <td class="numeric"><?php echo $total_maisdquatro != 0 ? $total_maisdquatro: ''; ?></td>
                    <td class="numeric"><?php echo $total_meses ?></td>
                </tr>
                
            </tfoot>
	</table>
	<?php
            echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
                    'renderTo' => 'grafico',
                    'chart' => array('type' => 'line'),
                    'yAxis' => array('title' => ''),
                    'xAxis' => array('labels' => array('rotation' => 0, 'y' => 20), 'gridLineWidth' => 1),
                    'tooltip' => array('formatter' => 'this.y'),
            )));
	?>
<?php endif; ?>