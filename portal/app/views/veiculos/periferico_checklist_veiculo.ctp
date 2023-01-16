<div class='form-procurar'>
    <?php echo $this->element('/filtros/periferico_checklist_veiculo'); ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
