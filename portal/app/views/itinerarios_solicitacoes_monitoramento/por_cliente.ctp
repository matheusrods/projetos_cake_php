<div class='form-procurar'>	
    <?php echo $this->element('/filtros/itinerarios_sms_por_cliente') ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>