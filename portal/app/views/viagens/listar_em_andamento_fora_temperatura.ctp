<?php
	echo $this->Javascript->codeBlock("
		if(typeof atualizaListaInterval != 'undefined')
			clearInterval(atualizaListaInterval);
	");
?>
<?php if(isset($filtros['codigo_cliente']) && $filtros['codigo_cliente']): ?>
	<div class="well">
		<strong>Última atualização:</strong> <?php echo date('d/m/Y H:i:s') ?> 
        <span class="pull-right">
            <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>   
        </span>
	</div>

    
	<div class='row-fluid'>
		<table class='table table-striped'>
			<thead>
		        <tr>
		            <th>SM</th>
		            <th>Placa</th>
		            <th>Data Início</th>
		            <th>Posição Atual</th>
		            <th>No Alvo</th>
		            <th>Mínima</th>
		            <th>Máxima</th>
		            <th>Atual</th>
		        </tr>
		    </thead>
		    <tbody>
		        <?php foreach($viagens as $viagem): ?>
		        	<?php if($viagem[0]['na_faixa'] == 0): ?>
				        <tr>
				            <td><?php echo $this->Buonny->codigo_sm($viagem['TViagViagem']['viag_codigo_sm']); ?></td>
				            <td><?php echo $this->Buonny->placa($viagem['TVeicVeiculo']['veic_placa'],date('d/m/Y 00:00:00'), date('d/m/Y 23:59:59'),$filtros['codigo_cliente']); ?></td>
				            <td><?php echo $viagem['TViagViagem']['viag_data_inicio']; ?></td>
				            <td style="max-width:250px;text-overflow:ellipsis;white-space: nowrap;overflow-x:hidden;"><?php echo $this->Buonny->posicao_geografica($viagem['TUposUltimaPosicao']['upos_descricao_sistema'],$viagem['TUposUltimaPosicao']['upos_latitude'],$viagem['TUposUltimaPosicao']['upos_longitude']); ?></td>
				            <td style="max-width:250px;text-overflow:ellipsis;white-space: nowrap;overflow-x:hidden;"><?php echo $viagem[0]['refe_descricao']; ?></td>
				            <td class="numeric"><?php echo $viagem['TVtemViagemTemperatura']['vtem_valor_minimo']; ?></td>
				            <td class="numeric"><?php echo $viagem['TVtemViagemTemperatura']['vtem_valor_maximo']; ?></td>
				            <td class="numeric"><?php echo $viagem['TUrpeUltimoRecPeriferico']['urpe_valor']; ?></td>
				        </tr>
				    <?php endif; ?>
		        <?php endforeach; ?>        
		    </tbody>
		    <tfoot>
	            <tr>
	                <td style="font-weight:bold;">Total: <?php echo count(array_filter($viagens, function($var){ return $var[0]['na_faixa'] == 0; })); ?></td>
	                <td colspan="7"></td>
	            </tr>
	        </tfoot>
		</table>
	</div>
	<?php
		echo $this->Javascript->codeBlock("
			jQuery(document).ready(function(){
				atualizaListaInterval = setInterval(function(){
					atualizaListaViagensForaTemperatura();					
				}, 2000);
			});
		");
	?>
<?php endif; ?>