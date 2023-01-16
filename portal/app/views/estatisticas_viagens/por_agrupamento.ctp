<div class='form-procurar'>
    <?php echo $this->element('/filtros/estatisticas_viagens_por_agrupamento'); ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('estatisticas2')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>