<div class='well'>
	<?php echo $bajax->form('FichaPsicossocial', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaPsicossocial', 'element_name' => 'ficha_psicossocial_terceiros'), 'divupdate' => '.form-procurar')) ?>
	
		<?php echo $this->element('ficha_psicossocial/filtros_ficha_psicossocial_terceiros'); ?>

		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
		<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>

	<?php echo $this->BForm->end() ?>
</div>	
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	setup_datepicker();
        setup_mascaras();
				
		var div = jQuery(".lista");
		bloquearDiv(div);
		div.load(baseUrl + "ficha_psicossocial/lista_psicossocial_terceiros/" + Math.random());

		jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaPsicossocial/element_name:ficha_psicossocial_terceiros/" + Math.random())
		});

		$(".datepickerjs").datepicker({
            dateFormat: "dd/mm/yy",
            showOn : "button",
            buttonImage : baseUrl + "img/calendar.gif",
            buttonImageOnly : true,
            buttonText : "Escolha uma data",
            dayNames : ["Domingo","Segunda","Terça","Quarta","Quinta","Sexta","Sabado"],
            dayNamesShort : ["Dom","Seg","Ter","Qua","Qui","Sex","Sab"],
            dayNamesMin : ["D","S","T","Q","Q","S","S"],
            monthNames : ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
            monthNamesShort : ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"],
            onClose : function() {
            }
        }).mask("99/99/9999");		
    });', false);
?>