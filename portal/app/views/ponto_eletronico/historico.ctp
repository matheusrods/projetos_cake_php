<div class="form-procurar">
    <?php echo $this->element('/filtros/historico_registro_de_ponto'); ?>
</div>
<div class="lista"></div>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>