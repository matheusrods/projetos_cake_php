<div class='form-procurar'>
    <?php echo $this->element('/filtros/relatorios_sm_acompanhamento_viagens_sintetico'); ?>
</div>
<div align="right">
	<?php echo $html->link('Atualização Automática: Desativado', 'javascript:autoRefresh();', array('class' => 'auto-refresh')) ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>