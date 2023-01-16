<div class = 'form-procurar'>
	<?= $this->element('/filtros/funcionarios_liberacao_trabalho') ?>
</div>

<div id='importacao_pedidos_exame' class="row-fluid inline text-right control-group"></div>
<div class='lista' id='lista'></div>

<?php echo $this->Javascript->codeblock("
	$(document).ready(function() { setup_mascaras() });
", false); ?>