<div class='form-procurar'>	
   	<?php echo $this->element('/filtros/taxa_administrativa_sintetica');?>
</div>
<div class='lista'></div>
<?php echo $this->Javascript->codeBlock("
	function atualizaListaTaxaAdministrativaSintetica(){
		var div = jQuery('div.lista');
		bloquearDiv(div);
		div.load(baseUrl + 'itens_pedidos/taxa_administrativa_sintetica_listagem/' + Math.random());
	}
") ?>