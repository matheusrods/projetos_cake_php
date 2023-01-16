<div class = 'form-procurar'>
    <?= $this->element('/filtros/pesquisa_satisfacao_sintetico') ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>