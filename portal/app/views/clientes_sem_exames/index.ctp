<div class = 'form-procurar'>
	<?= $this->element('/filtros/clientes_sem_exames') ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>