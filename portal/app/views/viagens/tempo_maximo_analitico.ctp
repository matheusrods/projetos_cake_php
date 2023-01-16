<div class = 'form-procurar'>
    <?= $this->element('/filtros/tempo_maximo_analitico') ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php $this->addScript($this->Buonny->link_js('alvos')) ?>
<?php $this->addScript($this->Buonny->link_js('bootstrap-multiselect')); ?>