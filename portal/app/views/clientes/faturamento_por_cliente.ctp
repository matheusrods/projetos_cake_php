<div class='form-procurar'>	
	<?= $this->element('/filtros/faturamento_por_cliente') ?>
</div>
<div class="lista"></div>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>