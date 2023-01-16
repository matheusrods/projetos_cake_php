<div class="well">
    <div class="span3">
        <strong>Última atualização: </strong> <?php echo date('d/m/Y H:i:s') ?>
    </div>
</div>
    <table class='table table-striped veiculos'>
	    <thead>
	        <tr>
	            <th><?php echo $this->Html->link('Veículo', 'javascript:void(0)') ?></th>
	            <th><?php echo $this->Html->link('Placa', 'javascript:void(0)') ?></th>
	            <th><?php echo $this->Html->link('SM', 'javascript:void(0)') ?></th>
	            <th><?php echo $this->Html->link('Trânsito', 'javascript:void(0)') ?></th>
	            <th><?php echo $this->Html->link('Origem', 'javascript:void(0)') ?></th>
	            <th><?php echo $this->Html->link('Trânsito', 'javascript:void(0)') ?></th>
	            <th><?php echo $this->Html->link('Cliente', 'javascript:void(0)') ?></th>	            
	        </tr>
	    </thead>
	    <tbody>
    		<?php 
			$status = array('AD' => 'Antecipado', 'AT' => 'Atrasado', 'SP' => 'Sem Posição', 'NO' => 'No Prazo', 'NO SM' => 'Sem Viagem', 'LO' => 'Logístico' );
    		foreach($dados as $dado): 
    			$dado = $dado[0];
    			?>
	    	<tr>
	    		<td><?php echo $dado['veic_oras_codigo'] ?></td>
<td><?php 

echo $this->Buonny->placa(
	$dado['veic_placa'], date('d/m/Y 00:00:00'), date('d/m/Y H:i:s'), $filtros['VeiculoPosicaoFrota']['codigo_cliente']) ;

?></td>
<td><?php echo $this->Buonny->codigo_sm($dado['viag_codigo_sm']); ?></td>
	    		<td><?php echo !empty($status[$dado['transito']]) ? $status[$dado['transito']] : '';?></td>
	    		<td><?php echo !empty($status[$dado['origem']]) ? $status[$dado['origem']] : '';?></td>
	    		<td><?php echo !empty($status[$dado['transito2']]) ? $status[$dado['transito2']] : '';?></td>
	    		<td><?php echo !empty($status[$dado['clientes']]) ? $status[$dado['clientes']] : '';?></td>	    		
	    	</tr>
	    	<?php endforeach; ?>
    	</tbody>
	    <tfoot>
	    	<tr>
	        	<td colspan="7"><strong>Total de veículos: <?php echo count($dados); ?></strong></td>
	            
			</tr>
	   	</tfoot>
</table>
<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){   
    jQuery("table.veiculos").tablesorter({ widgets: ["zebra"] });
 });', false); 