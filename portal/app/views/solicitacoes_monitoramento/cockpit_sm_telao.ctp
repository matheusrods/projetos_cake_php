<style type="text/css">
    .page-title{ width: 800px; height: 44px; float: left; padding-top: 20px; margin-bottom: 35px; }
    .page-title h3{ text-align: right; width: 500px; margin: 0 auto; }
    #logo{ width: 200px; height: 58px; float: right; }
    .lista{ clear: both; width: 100%; min-height: 100px; }
    #info-pagina{ font-size: 18px; }    
</style>

<div id="logo">
    <img src="http://www.buonny.com.br/images/logo_situacao_monitoramento.jpg" border="0" />
</div>

<div class="lista">
</div>

<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php echo $this->Buonny->link_js('solicitacoes_monitoramento') ?>

<?php echo $this->Javascript->codeBlock("
    carregaDadosTelaoBuonnySatSMsEncerradas();
"); ?>