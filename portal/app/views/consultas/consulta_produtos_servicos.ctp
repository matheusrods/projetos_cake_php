<div class = 'form-procurar'>
	<?= $this->element('/filtros/produtos_servicos') ?>
</div>
<div class='lista'></div>

<?php echo $this->Javascript->codeblock("
	$(document).ready(function() { setup_mascaras() });
", false); ?>