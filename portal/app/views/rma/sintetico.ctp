<div class = 'form-procurar'>
    <?= $this->element('/filtros/rma_sintetico')?>
</div>
<div class='row-fluid'>
    <div id='graph' style="min-height: 400px">
        <div id="agrp"></div>
    </div>
</div>
<div class='row-fluid'>
    <div id='table-dados'></div>
</div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>