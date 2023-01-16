<link href="/portal/css/layouts/listagem.css" rel="stylesheet" />
<div class='form-procurar'>
	<?= $this->element('/filtros/importacao_layouts') ?>
</div>
<div class="lista"></div>
<?php
	$this->addScript($this->Buonny->link_js('comum.js'));
?>