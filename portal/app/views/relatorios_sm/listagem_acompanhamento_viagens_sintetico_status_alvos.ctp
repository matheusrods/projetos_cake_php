<?php if (empty($relatorioStatusAlvo)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
	<table class='table table-striped status-alvos'>
	    <thead>
	        <tr>
	            <th><?= $this->Html->link($agrupamento_label, 'javascript:void(0)') ?></th>
	            <th class='numeric'><?= $this->Html->link('Entregando', 'javascript:void(0)') ?></th>
	            <th class='numeric'><?= $this->Html->link('Entregue', 'javascript:void(0)') ?></th>
	            <th class='numeric'><?= $this->Html->link('A entregar', 'javascript:void(0)') ?></th>
	            <th class='numeric'><?= $this->Html->link('Total', 'javascript:void(0)') ?></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php
	        	$totais = array('agrupamento'=>'Total', 'entregando'=>0, 'entregue'=>0, 'a_entregar'=>0, 'total'=>0);
	        	foreach($relatorioStatusAlvo as $registro): 
        	?>
		        <?php 
		        	$registro = $registro[0];
		        	foreach(array_slice($registro, 2) as $campo=>$valor)
		        		$totais[$campo] += $valor;
	        	?>
		        <tr>
		            <td><?php echo empty($registro['agrupamento']) ? 'Não definido' : $registro['agrupamento']; ?></td>
 		            <td class='numeric'>
 		            	<?php echo $this->Html->link($this->Buonny->moeda($registro['entregando'], array('nozero'=>true, 'places'=>0)), array('action'=>'listagem_acompanhamento_viagens_analitico', 'popup' , 1, $registro['codigo']), array('onclick'=>'return open_popup(this);')); ?>
 		            	<?php if($registro['entregando'] > 0) echo '('.$this->Buonny->moeda($registro['entregando'] / ($registro['total']) * 100).'%)' ?>
 		            </td>
 		            <td class='numeric'>
 		            	<?php echo $this->Html->link($this->Buonny->moeda($registro['entregue'], array('nozero'=>true, 'places'=>0)), array('action'=>'listagem_acompanhamento_viagens_analitico', 'popup' , 2, $registro['codigo']), array('onclick'=>'return open_popup(this);')); ?>
 		            	<?php if($registro['entregue'] > 0) echo '('.$this->Buonny->moeda($registro['entregue'] / ($registro['total']) * 100).'%)' ?>
 		            </td>
 		            <td class='numeric'>
 		            	<?php echo $this->Html->link($this->Buonny->moeda($registro['a_entregar'], array('nozero'=>true, 'places'=>0)), array('action'=>'listagem_acompanhamento_viagens_analitico', 'popup' , 3, $registro['codigo']), array('onclick'=>'return open_popup(this);')); ?>
 		            	<?php if($registro['a_entregar'] > 0) echo '('.$this->Buonny->moeda($registro['a_entregar'] / ($registro['total']) * 100).'%)' ?>
 		            </td>
 		            <td class='numeric'><?php echo $this->Html->link($this->Buonny->moeda($registro['total'], array('nozero'=>true, 'places'=>0)), array('action'=>'listagem_acompanhamento_viagens_analitico', 'popup' , 0, $registro['codigo']), array('onclick'=>'return open_popup(this);')); ?></td>
		        </tr>
	        <?php endforeach; ?>  
	    </tbody>
	    <tfoot>
	        <tr>
	            <td><strong><?php echo $totais['agrupamento'] ?></strong></td>
	            <td class='numeric'>
	            	<strong><?php echo $this->Html->link($this->Buonny->moeda($totais['entregando'], array('nozero'=>true, 'places'=>0)), array('action'=>'listagem_acompanhamento_viagens_analitico', 'popup' , 1), array('onclick'=>'return open_popup(this);')); ?></strong>
 		            <?php if($totais['entregando'] > 0) echo '('.$this->Buonny->moeda($totais['entregando'] / ($totais['total']) * 100).'%)' ?>
            	</td>
	            <td class='numeric'>
	            	<strong><?php echo $this->Html->link($this->Buonny->moeda($totais['entregue'], array('nozero'=>true, 'places'=>0)), array('action'=>'listagem_acompanhamento_viagens_analitico', 'popup' , 2), array('onclick'=>'return open_popup(this);')); ?></strong>
 		            <?php if($totais['entregue'] > 0) echo '('.$this->Buonny->moeda($totais['entregue'] / ($totais['total']) * 100).'%)' ?>
            	</td>
	            <td class='numeric'>
	            	<strong><?php echo $this->Html->link($this->Buonny->moeda($totais['a_entregar'], array('nozero'=>true, 'places'=>0)), array('action'=>'listagem_acompanhamento_viagens_analitico', 'popup' , 3), array('onclick'=>'return open_popup(this);')); ?></strong>
 		            <?php if($totais['a_entregar'] > 0) echo '('.$this->Buonny->moeda($totais['a_entregar'] / ($totais['total']) * 100).'%)' ?>
            	</td>
	            <td class='numeric'><strong><?php echo $this->Html->link($this->Buonny->moeda($totais['total'], array('nozero'=>true, 'places'=>0)), array('action'=>'listagem_acompanhamento_viagens_analitico', 'popup'), array('onclick'=>'return open_popup(this);')); ?></strong></td>
	        </tr>
	    </tfoot>
	</table>
	<?php echo $this->Javascript->codeBlock('
	    jQuery(document).ready(function(){
			$.tablesorter.addParser({
				debug:true,
				id: "qtd", 
				is: function(s) { 
					// return false so this parser is not auto detected 
					// poderia ser detectado pelo simbolo do real R$
					return false;
				},
				format: function(s) { 
				   return $.tablesorter.formatInt(s.replace(new RegExp(/\(\d*\)/g),""));
				}, 
				type: "numeric"
			});
			
			jQuery("table.status-alvos").tablesorter({
				headers: {
					1: {sorter: "qtd"},
					2: {sorter: "qtd"},
					3: {sorter: "qtd"}
				},
				widgets: ["zebra"]
			});
	    });', false);
	?>
<?php endif; ?>