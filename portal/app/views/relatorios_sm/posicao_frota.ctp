<div class='form-procurar'>
    <?php echo $this->element('/filtros/veiculos_posicao_frota'); ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('abre_relatorios_sm')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>