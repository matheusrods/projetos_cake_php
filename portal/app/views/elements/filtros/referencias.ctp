<div class='well'>
	<?php echo $bajax->form('Referencia', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Referencia', 'element_name' => 'referencias'), 'divupdate' => '.form-procurar')) ?>
  		<?php echo $this->element('referencias/fields_filtros') ?>
	<?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){
        atualizaListaReferencias();
        $("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "/filtros/limpar/model:Referencia/element_name:referencias/" + Math.random())
        });

		$("a#filtros").click(function(){
			$("div#filtros").slideToggle("fast");
		});
        function atualizaListaReferencias() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "referencias/listagem/" + Math.random());    
        }
    });', false);
?>