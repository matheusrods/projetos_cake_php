<div class = 'form-procurar'><?= $this->element('/filtros/pcmso_versoes') ?></div>
<div class='lista' id='lista'></div>
<?php echo $this->Javascript->codeblock("
	$(document).ready(function() { setup_mascaras() });
", false); ?>