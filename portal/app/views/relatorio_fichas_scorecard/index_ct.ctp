<div class = 'form-procurar'>
    <?= $this->element('/filtros/relatorio_fichas_scorecard_ct') ?>
</div>
<div class='actionbar-right'>
</div>
<div id="cliente" class='well'>
        <span class="pull-right">
        <?php echo $this->Html->link('<i class="icon-print dialog"></i>', array('controller' => $this->name, 'action' => 'gera_ct') , array('escape' => false, 'title' => 'Imprimir Demonstrativo CT')); ?>  
        </span>
</div>
<div class='lista'></div> 
