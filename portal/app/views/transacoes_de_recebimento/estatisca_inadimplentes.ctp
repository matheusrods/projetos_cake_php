<div class='well'>
	<?php echo $this->BForm->create('Tranrec', array('url' => array('controller' => 'TransacoesDeRecebimento', 'action' => 'estatisca_inadimplentes'))); ?>
	<div class="row-fluid inline">
        <?php echo $this->BForm->input('ano', array('label' => false, 'placeholder' => 'Ano','class' => 'input-small', 'type' => 'select', 'options' => $anos ,'default'=>$ano_atual)) ?>
        <?php echo $this->Buonny->input_grupo_empresas($this,$grupos_empresas, $empresas); ?>
	</div>
    <div class="row-fluid inline">
	   <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	   <?= $html->link('Limpar Filtros', array('controller' => 'TransacoesDeRecebimento', 'action' => 'estatisca_inadimplentes'), array('class' => 'btn')) ?>
       <?php echo $this->BForm->end() ?>
    </div>
</div>    

<?php if(isset($dados)): ?>
    
    <?php if(isset($nome_grupo)):?>
        <div class="well">
            <strong>Grupo: </strong><?php echo $nome_grupo; ?>
            <strong>Empresa: </strong><?php echo (!empty($empresa) ? $empresa['LojaNaveg']['razaosocia'] : 'Todas empresas'); ?>
            <strong> Ano : </strong><?= $ano;?>
        </div>
    <?php endif?>    
    
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th> Mês </th>
                <th class="numeric"> Faturamento Bruto(R$)</th>
                <th class="numeric"> Faturamento Líquido(R$)</th>
                <th class="numeric"> Inadimplência (R$) </th>
                <th class="numeric">Inadimplência (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php $total_porcento_inadimplente = 0;?>
            <?php $total_inadimplentes         = 0;?>
            <?php $total_faturamento           = 0;?>
            <?php $total_liquido               = 0;?>

            <?php foreach($dados as $key => $dado): ?>
                <?php $mes = str_replace("/","",substr($dado[0]['ano_mes'], 0, 2)); ?>
                 <?php $ano_mes =  str_replace("/","-",$dado[0]['ano_mes']);?> 
	                
	                <tr>
	                    <td><?php echo $this->Buonny->mes_extenso($mes); ?></td>
	                    <td class="numeric"><?php echo $this->Buonny->moeda($dado[0]['valor_total_merc']);?></td>
	                    <td class="numeric"><?php echo $this->Buonny->moeda($dado[0]['liquido']); ?></td>
	                    
	                    <td class="numeric">
	                    <?php if($dado[0]['inadimplentes'] == 0):?>
	                    	<?php echo $this->Buonny->moeda($dado[0]['inadimplentes'])?>
	                    <?php else:?>	
	                    	<?php echo $this->Html->link( $this->Buonny->moeda($dado[0]['inadimplentes']),array('controller'=>'TransacoesDeRecebimento', 'action'=>'total_titulos_clientes', $ano_mes),array('onclick'=>"return open_popup(this);")); ?></td>
	                    <?php endif ?>
	                    <td class="numeric"><?php echo $this->Buonny->moeda($dado[0]['porcento_inadimplente']); ?></td>
	                </tr>
		            		                
	                <?php $total_inadimplentes         = $total_inadimplentes         + $dado[0]['inadimplentes'];?>
	                <?php $total_faturamento           = $total_faturamento           + $dado[0]['valor_total_merc'];?>
	                <?php $total_liquido               = $total_liquido               + $dado[0]['liquido'];      ?>
					        
	        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td class="numeric"><?php echo $this->Buonny->moeda(round($total_faturamento, 2)) ?></td>
                <td class="numeric"><?php echo $this->Buonny->moeda(round($total_liquido, 2)) ?></td>
                <td class="numeric"><?php echo $this->Buonny->moeda(round($total_inadimplentes, 2)) ?></td>
                <?php if($total_inadimplentes == 0 || $total_liquido == 0 ):?>
                    <td class="numeric"><?php echo $this->Buonny->moeda(round($total_porcento_inadimplente,2))?></td>
                <?php else: ?>
                    <td class="numeric"><?php echo $this->Buonny->moeda(round(($total_inadimplentes*100)/$total_liquido, 2)) ?></td>
                <?php endif?>   
            </tr>
        </tfoot>
    </table>
<?php endif; ?>
