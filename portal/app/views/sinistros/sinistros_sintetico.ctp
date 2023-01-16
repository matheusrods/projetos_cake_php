<div class='form-procurar'>
    <?php echo $this->element('/filtros/sinistros_sintetico'); ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>

<!-- http://portal.localhost/portal/rma/sintetico -->