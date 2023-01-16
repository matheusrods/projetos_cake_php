<div class='form-procurar'>	
   	<?php echo $this->element('/filtros/taxa_administrativa_analitica');?>
</div>
<div class='lista'></div> 

<?php echo $this->Javascript->codeBlock("
	function atualizaListaTaxaAdministrativaAnalitica(){
		var div = jQuery('div.lista');
		bloquearDiv(div);
		div.load(baseUrl + 'itens_pedidos/taxa_administrativa_analitica_listagem/' + Math.random());
	}
") ?>