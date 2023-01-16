<div class='form-procurar'>	
    <?php echo $this->element('/filtros/duracao_sm') ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>