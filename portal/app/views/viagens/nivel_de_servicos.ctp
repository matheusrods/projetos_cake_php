<div class = 'form-procurar'>
    <?= $this->element('/filtros/nivel_de_servicos') ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('abre_relatorios_sm')) ?>
<?php $this->addScript($this->Buonny->link_js('alvos')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>