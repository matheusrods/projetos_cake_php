<?php if(!empty($dados)): ?>
    <div class="row-fluid">
        <?php
            $linha_impar = false;
            foreach($dados as $codigoTipoVeiculo => $tipoVeiculo):
                $linha_impar = !$linha_impar;
        ?>
        	<table class="table table-striped" style="float:left; width:49.5%; <?php echo $linha_impar ? 'clear:both; margin-right: 10px;' : '' ?>">
        	    <thead>
        	        <tr>
        	            <th><?php echo $tipoVeiculo['descricao']; ?></th>
        	            <?php foreach($tipoVeiculo['referencias'] as $referencia): ?>
        	                <th class='numeric'><?php echo $referencia; ?></th>
        	            <?php endforeach; ?>
        	            <th class='numeric'>Total</th>
        	        </tr>
        	    </thead>
        	    <tbody>
        	        <?php foreach($tipoVeiculo['totais'] as $key_total => $total): ?>
        		        <tr>
        		            <td><?php echo Inflector::humanize(strtolower($total['cref_descricao'])); ?></td>
        		            <?php foreach($tipoVeiculo['referencias'] as $key_referencia => $referencia): ?>
        		                <td class='numeric'><?php echo isset($total['valores'][$key_referencia]) ? $this->Html->link($this->Buonny->moeda($total['valores'][$key_referencia], array('nozero'=>true, 'places'=>0)), array('action'=>'situacao_frota_analitico', 'tipo_veiculo' => $codigoTipoVeiculo, 'cref_codigo' => $key_total, 'cd' => $key_referencia), array('onclick'=>'return open_popup(this);')) : ''; ?></td>
        		            <?php endforeach; ?>
        		            <td class='numeric'><?php echo $this->Buonny->moeda(array_sum($total['valores']), array('nozero'=>true, 'places'=>0)); ?></td>
        		        </tr>
        	        <?php endforeach; ?>
        	        
        	        <?php $total = $tipoVeiculo['total_linha']; ?>
        	        <tr>
    		            <td><?php echo $total['cref_descricao']; ?></td>
    		            <?php foreach($tipoVeiculo['referencias'] as $key_referencia => $referencia): ?>
    		                <td class='numeric'><?php echo isset($total['valores'][$key_referencia]) ? $this->Buonny->moeda($total['valores'][$key_referencia], array('nozero'=>true, 'places'=>0)) : ''; ?></td>
    		            <?php endforeach; ?>
    		            <td class='numeric'><?php echo $this->Buonny->moeda(array_sum($total['valores']), array('nozero'=>true, 'places'=>0)); ?></td>
    		        </tr>
        	    </tbody>
        	</table>
        <?php endforeach; ?>
    </div>
<?php else: ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php endif; ?>