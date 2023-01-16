<div class = 'form-procurar'>
	<?= $this->element('/filtros/consulta_vidas') ?>
</div>
<div class='lista' id='lista'></div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
		jQuery("#bt-sintetico").click(function(){
			var div = jQuery(".lista");
			bloquearDiv(div);
			//div.load(baseUrl + "clientes_funcionarios/consulta_vidas_analitico_listagem/" + Math.random());				
        });
        // jQuery("#bt-sintetico").click();
    });', false);
?>