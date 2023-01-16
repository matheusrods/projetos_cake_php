<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
	var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "operadores/viagens_sem_operadores_listagem/" + Math.random());
});', false);	
?>