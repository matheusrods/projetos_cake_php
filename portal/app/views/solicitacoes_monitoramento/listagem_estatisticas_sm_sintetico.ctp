<div class='row-fluid inline'>
	<?php if(isset($dados) && count($dados) > 0): ?>
	    <?php 
			switch($group){
				case 1: 
					$fields = 'Pagador'; 
					break;
				case 2:
					$fields = 'Embarcador'; 
					break;
				case 3:
					$fields = 'Transportador'; 
					break;
			}
		?>
		<?php echo $this->Paginator->options(array('update' => '.lista')); ?>
		 <table class='table table-striped tablesorter'>
		    <thead>
		        <tr>
		            <th><?= $this->Paginator->sort('Documento', 'Recebsm.codigo_documento') ?> </th>
		            <th><?= $this->Paginator->sort( $fields,'Recebsm.razao_social')?></th>
		            <th class="numeric"><?= $this->Paginator->sort('Média','Recebsm.valor_media')?></th>
		            <th class="numeric"><?= $this->Paginator->sort('Valor Transp','Recebsm.valor_total')?></th>
		            <th class="numeric"><?= $this->Paginator->sort('QTD','Recebsm.quantidade')?></th>
		        </tr>
		    </thead>
		    <tbody>
					
				    <?php foreach ($dados as $dado): ?>
					    <tr>
					        <td><?= $dado['Recebsm']['codigo_documento'] ?></td>
					        <td><?= $dado['Recebsm']['codigo'].' - '.$dado['Recebsm']['razao_social'] ?></td>
					        <td class="numeric"><?= $this->Buonny->moeda($dado['Recebsm']['valor_media'],array('nozero'=>true)); ?></td>
					        <td class="numeric"><?= $this->Buonny->moeda($dado['Recebsm']['valor_total'], array('nozero' => true)) ?></td>
					        <td class='numeric'><?php
							echo $this->Html->link(
                            $dado['Recebsm']['quantidade'],
                            array('action'=>'estatisticas_sm_analitico', 'popup', $group, $dado['Recebsm']['codigo']), 
                            array('onclick'=>'return open_popup(this);')); 
                            ?></td>
					    </tr>
				    <?php endforeach; ?>           
		    </tbody>
		    <tfoot>
	            <tr>
	                <td colspan="5"><strong>Total: </strong><?php echo $this->Paginator->counter(array('format' => '%count%')); ?></td>
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
    <?php else: ?>
        <?php if (isset($dados) && count($dados)==0): ?>
            <div class="alert">Nenhum dado foi encontrado.</div>
        <?php endif ?>
    <?php endif ?>
</div>	       
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter({sortList: [[0,1]], headers: {3: {sorter:false}} })")) ?> 
<?php echo $this->Javascript->codeBlock("
            jQuery(document).ready(function(){
                $('.numbers a[id^=\"link\"]').bind('click', function (event) { bloquearDiv($('.lista')); });
            });", false);
        ?>