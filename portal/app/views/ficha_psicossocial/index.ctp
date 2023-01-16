<div class = 'form-procurar'>
	<?= $this->element('/filtros/ficha_psicossocial') ?>
</div>
<div class='lista' id='lista'></div>

<?php echo $this->Javascript->codeblock("
	$(document).ready(function() { setup_mascaras() });
	", false); ?>