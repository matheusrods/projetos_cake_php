<?php
    echo $paginator->options(array('update' => 'div#historico'));
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>

        	<th>Unidade</th>
		    <th>Setor</th>
		    <th>Cargo</th>
		    <th>Data de Atendimento</th>
		    <th>Observação</th>
        </tr>
    </thead>
    <tbody>
    	<?php if(is_array($historico) || is_object($historico)) : ?>
	        <?php foreach ($historico as $hist ): ?>
	        <tr>
	            <td><?= $hist['Cliente']['razao_social'] ?></td>
	            <td><?= $hist['HistoricoFichaClinica']['setor'] ?></td>
	            <td><?= $hist['HistoricoFichaClinica']['cargo'] ?></td>
	            <td><?= $hist['HistoricoFichaClinica']['data_atendimento'] ?></td>
	            <td title="<?= $hist['HistoricoFichaClinica']['observacoes'] ?>">
	            	
	            	<?php if(trim($hist['HistoricoFichaClinica']['observacoes']) != ''): ?>
	            		<textarea name="data[HistoricoFichaClinica][observacoes]" class="input-xxlarge" cols="30" rows="10" id="HistoricoFichaClinicaObservacoes" readonly="readonly" ><?= $hist['HistoricoFichaClinica']['observacoes'] ?></textarea>
	            	<?php else: ?>
	            		<?= $hist['HistoricoFichaClinica']['observacoes'] ?>
	            	<?php endif; ?>
	            	
	           	</td>
	        </tr>
	        <?php endforeach; ?>    		
    	<?php endif; ?>
    </tbody>
</table>
<div class='row-fluid'>
	<div class='numbers span6'>
		<?php if($total_paginas > 1) : ?>
			<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
			<?php echo $this->Paginator->numbers(); ?>
			<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>		
		<?php endif; ?>
	</div>
	<div class='counter span6'>
	    <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	</div>
</div>
<?php echo $this->Js->writeBuffer(); ?>