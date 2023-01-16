<div class="lista">
<?php 
    echo $this->Paginator->options(array('update' => '.lista')); 
?>


<?php if(isset($dados)): ?>
    <h3>Títulos por Clientes</h3>
    <?php if(isset($nome_grupo)):?>
        <div class="well">
            <strong>Grupo: </strong><?php echo $nome_grupo; ?>
            <strong>Empresa: </strong><?php echo (!empty($empresa) ? $empresa['LojaNaveg']['razaosocia'] : 'Todas empresas'); ?>
            <strong> Período : </strong><?= $this->Buonny->mes_extenso(str_replace("/","",substr($dados[0][0]['ano_mes'], 0, 2)));?> 
        </div>
    <?php endif?> 

    
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="input-xxlarge"> Cliente </th>
                <th class="numeric">Títulos(QTD)</th>
                <th class="numeric"> Valor(R$)</th>
            </tr>
        </thead>
        <tbody>
            
            
            <?php foreach($dados as $key => $dado): ?>
                
                <?php $ano_mes =  str_replace("/","-",$dado[0]['ano_mes']);?>                     
                <tr>
                    <td class="input-xxlarge"><?php echo $dado[0]['Cliente'] ?></td>
                    <td class="numeric">
                    <?php echo $this->Html->link($dado[0]['Titulos'],array('controller'=>'TransacoesDeRecebimento', 'action'=>'titulos_clientes',$dado[0]['codigo'],$ano_mes),array('onclick'=>"return open_popup(this);",'title'=>'Visualizar Títulos'));?>
                    </td>
                    
                    <td class="numeric"><?php echo $this->Buonny->moeda($dado[0]['valor_total']); ?></td>
                </tr>
		    <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                
                <td><strong>Total</strong></td>
                <td class="numeric"><strong><?php echo $total_titulos ?>&nbsp;Título(s)</strong></td>
                <td class="numeric"><strong><?php echo $this->Buonny->moeda(round($total_valor, 2)) ?><strong></td>
                
            </tr>
        </tfoot>
    </table>
<?php endif; ?>

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
	<?php echo $javascript->link('comum.js'); ?>
</div>