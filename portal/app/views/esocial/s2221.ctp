<div class = 'form-procurar'>
	<?= $this->element('/filtros/s2221') ?>
</div>

<div class='lista' id='lista'></div>

<?php echo $this->Javascript->codeblock("
	$(document).ready(function() { setup_mascaras() });
", false); ?>