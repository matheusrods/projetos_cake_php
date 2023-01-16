
<div class="lista">
<?php 
    echo $this->Paginator->options(array('update' => '.lista')); 
?>
<h3>Títulos - Cliente</h3>
    
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
               <th class="input-xxlarge">Cliente</th> 
               <th class="input-large">Número Título</th>     
               <th class="numeric">Ordem</th>     
               <th class="numeric">Seq</th>     
               <th class="input-large">Data Emissão</th> 
               <th class="input-large">Data Vencimento</th>             
               <th class="numeric">Valor(R$)</th>     
               <th class="input-medium">Dias Vencidos</th> 
               <th class="input-xxlarge">Histórico</th> 
            </tr>
        </thead>
        <tbody>
            <?php foreach($dados as $key => $dado): ?>
                <tr>
                    <td><?php echo  $dado[0]['clientes']?></td>
                    <td class="numeric"><?php echo $dado[0]['nota_numero']?></td>
                    <td class="numeric" ><?php echo $dado[0]['ordem']?></td>
                    <td class="numeric"> <?php echo $dado[0]['seq']?></td>
                    <td><?php echo $dado[0]['data_emiss'] ?></td>
                    <td><?php echo $dado[0]['data_venc']?></td>
                    <td class="numeric"><?php echo $dado[0]['valor']?></td>
                    <td class="numeric"><?php echo $dado[0]['dias_venc']?></td>
                    <td><?php echo $dado[0]['obs']?></td>
                </tr>
                                        
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td><strong>Total</strong></td>
                <td class="numeric"><strong><?php echo $total_titulos ?> Título(s) </strong></td>
                <td colspan="4"></td>
                <td class="numeric"><strong><?php echo $this->Buonny->moeda(round($total_valor, 2)) ?><strong></td>
                <td colspan="2"></td>    
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
</div>  
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $javascript->link('comum.js'); ?>
