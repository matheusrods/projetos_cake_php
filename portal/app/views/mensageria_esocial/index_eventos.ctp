<div class = 'form-procurar'>
	<?= $this->element('/filtros/index_eventos') ?>
</div>

<div class='lista' id='lista'></div>

<?php echo $this->Javascript->codeblock("
	$(document).ready(function() { setup_mascaras() });
", false); ?>