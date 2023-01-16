<div class = 'form-procurar'>
	<?= $this->element('/filtros/consulta_vidas_analitico') ?>
</div>
<div class='lista' id='lista'></div>
<?php echo $this->Javascript->codeblock("
	$(document).ready(function() { setup_mascaras() });
", false); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		jQuery("#bt-analitico").click(function(){
			var div = jQuery(".lista");
			bloquearDiv(div);
			div.load(baseUrl + "clientes_funcionarios/consulta_vidas_analitico_listagem/" + Math.random());				
        });
        jQuery("#bt-analitico").click();
    });', false);
?>