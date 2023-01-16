<div class = 'form-procurar'>
    <?= $this->element('filtros/mods_ivrs_pesquisas_sintetico') ?>
</div>
<div class='grafico'></div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>